<?php

namespace tests\codeception\unit\models;

use app\models\DocumentInvoice;
use app\models\DocumentTransfer;
use app\models\Flows;
use app\models\Operation;
use app\models\Users;
use yii\codeception\TestCase;
use Codeception\Specify;

class BillingTest extends TestCase
{
    use Specify;

    public function testTransfer()
    {
        $userSender = Users::findByUsername('admin');
        $userRecipient = Users::findByUsername('user');

        $this->transfer($userSender, $userRecipient, 'Test transfer admin to user of 12.5', [
            'amount' => 12.5,
            'sender_begin' => 0.0,
            'sender' => -12.5,
            'recipient_begin' => 0.0,
            'recipient' => 12.5,
            'flows' => 2,
        ]);

        $this->transfer($userRecipient, $userSender, 'Test transfer user to admin of 2.5', [
            'amount' => 2.5,
            'sender_begin' => 12.5,
            'sender' => 10.0,
            'recipient_begin' => -12.5,
            'recipient' => -10.0,
            'flows' => 2,
        ]);
        $this->userFlow($userSender, [
            'begin' => 0.0,
            'debit' => 2.5,
            'credit' => 12.5,
            'end' => -10.0,
        ]);
        $this->userFlow($userRecipient, [
            'begin' => 0.0,
            'debit' => 12.5,
            'credit' => 2.5,
            'end' => 10.0,
        ]);

    }

    public function testInvoice()
    {
        $userSender = Users::findByUsername('admin');
        $userPayer = Users::findByUsername('user');

        $invoice = $this->invoice($userSender, $userPayer, 'Invoice from admin to user of 120.33', [
            'amount' => 120.33,
        ]);

        $this->invoice_accept($invoice, [
            'sender_begin' => 0,
            'sender' => 120.33,
            'payer_begin' => 0,
            'payer' => -120.33,
            'flows' => 2,
        ]);
        $this->userFlow($userSender, [
            'begin' => 0,
            'debit' => 120.33,
            'credit' => 0,
            'end' => 120.33,
        ]);
        $this->userFlow($userPayer, [
            'begin' => 0,
            'debit' => 0,
            'credit' => 120.33,
            'end' => -120.33,
        ]);
    }

    public function testTransferAndInvoice()
    {
        $userSender = Users::findByUsername('admin');
        $userRecipient = Users::findByUsername('user');

        $this->transfer($userSender, $userRecipient, 'Test transfer admin to user of 12.5', [
            'amount' => 12.5,
            'sender_begin' => 0,
            'sender' => -12.5,
            'recipient_begin' => 0,
            'recipient' => 12.5,
            'flows' => 2,
        ]);

        $invoice = $this->invoice($userSender, $userRecipient, 'Invoice from admin to user of 112.5', [
            'amount' => 112.5,
        ]);

        $this->invoice_accept($invoice, [
            'sender_begin' => -12.5,
            'sender' => 100,
            'payer_begin' => 12.5,
            'payer' => -100,
            'flows' => 2,
        ]);

        $this->userFlow($userSender, [
            'begin' => 0,
            'debit' => 112.5,
            'credit' => 12.5,
            'end' => 100,
        ]);
        $this->userFlow($userRecipient, [
            'begin' => 0,
            'debit' => 12.5,
            'credit' => 112.5,
            'end' => -100,
        ]);
    }

    /**
     * @param $invoice DocumentInvoice
     * @param $amounts
     */
    private function invoice_accept($invoice, $amounts)
    {

        $this->specify('Invoice accepted', function () use ($invoice) {
            $invoice->status = DocumentInvoice::STATUS_ACTIVE;
            expect('DocumentInvoice saved', $invoice->save())->true();
            expect('DocumentInvoice status ACTIVE', $invoice->status)->equals(DocumentInvoice::STATUS_ACTIVE);
        });

        $invoice->refresh();
        $operation = $invoice->operation;
        $this->specify('Invoice operation', function () use ($invoice, $operation) {
            expect('ACTIVE Invoice has operation', $operation)->notNull();
            expect('Operation has correct document type', $operation->document_type)->equals(Operation::DOCUMENT_TYPE_INVOICE);
            expect('Operation has correct value', $operation->value)->equals($invoice->value);
        });

        $this->usersBalance($invoice->user, $invoice->payer, $amounts['sender'], $amounts['payer'], 'payer');

        $this->specify('DocumentInvoice data flows', function () use ($operation, $invoice, $amounts) {

            expect('Operation has ' . $amounts['flows'] . ' data flows', $operation->flows)->count($amounts['flows']);

            $sender_begin = Operation::getBeginTotal($invoice->user, $operation);
            $sender_debit = Operation::getDebitTotal($invoice->user, $operation);
            $sender_credit = Operation::getCreditTotal($invoice->user, $operation);
            $sender_end = Operation::getEndTotal($invoice->user, $operation);

            $payer_begin = Operation::getBeginTotal($invoice->payer, $operation);
            $payer_debit = Operation::getDebitTotal($invoice->payer, $operation);
            $payer_credit = Operation::getCreditTotal($invoice->payer, $operation);
            $payer_end = Operation::getEndTotal($invoice->payer, $operation);

            expect('Sender Begin Balance', $sender_begin)->equals($amounts['sender_begin']);
            expect('Sender Debit', $sender_debit)->equals($invoice->value);
            expect('Sender Credit', $sender_credit)->equals(0);
            expect('Sender End Balance', $sender_end)->equals($amounts['sender']);

            expect('Payer Begin Balance', $payer_begin)->equals($amounts['payer_begin']);
            expect('Payer Debit', $payer_debit)->equals(0);
            expect('Payer Credit', $payer_credit)->equals($invoice->value);
            expect('Payer End Balance', $payer_end)->equals($amounts['payer']);
        });
    }

    /**
     * @param $userSender Users
     * @param $userPayer Users
     * @param $comment string
     * @param $amounts array
     * @return DocumentInvoice
     */
    private function invoice($userSender, $userPayer, $comment, $amounts)
    {
        $invoice = new DocumentInvoice([
            'user_id' => $userSender->id,
            'payer_id' => $userPayer->id,
            'comment' => $comment,
            'value' => $amounts['amount'],
        ]);
        $this->specify('Invoice', function () use ($invoice) {
            expect('DocumentInvoice saved', $invoice->save())->true();
            expect('DocumentInvoice status NOT_ACTIVE', $invoice->status)->equals(DocumentInvoice::STATUS_NOT_ACTIVE);
        });

        $operation = $invoice->operation;
        $this->specify('Invoice operation', function () use ($operation) {
            expect('NOT_ACTIVE Invoice has no operation', $operation)->null();
        });

        return $invoice;
    }


    /**
     * @param $userSender Users
     * @param $userRecipient Users
     * @param $comment string
     * @param $amounts array
     */
    private function transfer($userSender, $userRecipient, $comment, $amounts)
    {
        $transfer = new DocumentTransfer([
            'user_id' => $userSender->id,
            'recipient_id' => $userRecipient->id,
            'comment' => $comment,
            'value' => $amounts['amount'],
        ]);

        $this->specify('Money transfer', function () use ($transfer) {
            expect('DocumentTransfer saved', $transfer->save())->true();
        });

        $operation = $transfer->operation;
        $this->specify('Money transfer operation', function () use ($transfer, $operation) {
            expect('Money transfer has operation', $operation)->notNull();
            expect('Operation has correct document type', $operation->document_type)->equals(Operation::DOCUMENT_TYPE_TRANSFER);
            expect('Operation has correct value', $operation->value)->equals($transfer->value);
        });

        $this->usersBalance($userSender, $userRecipient, $amounts['sender'], $amounts['recipient'], 'recipient');

        $this->specify('DocumentTransfer data flows', function () use ($operation, $userSender, $userRecipient, $amounts) {

            expect('Operation has ' . $amounts['flows'] . ' data flows', $operation->flows)->count($amounts['flows']);

            $sender_begin = Operation::getBeginTotal($userSender, $operation);
            $sender_debit = Operation::getDebitTotal($userSender, $operation);
            $sender_credit = Operation::getCreditTotal($userSender, $operation);
            $sender_end = Operation::getEndTotal($userSender, $operation);

            $recipient_begin = Operation::getBeginTotal($userRecipient, $operation);
            $recipient_debit = Operation::getDebitTotal($userRecipient, $operation);
            $recipient_credit = Operation::getCreditTotal($userRecipient, $operation);
            $recipient_end = Operation::getEndTotal($userRecipient, $operation);

            expect('Sender Begin Balance', $sender_begin)->equals($amounts['sender_begin']);
            expect('Sender Debit', $sender_debit)->equals(0);
            expect('Sender Credit', $sender_credit)->equals($amounts['amount']);
            expect('Sender End Balance', $sender_end)->equals($amounts['sender']);

            expect('Recipient Begin Balance', $recipient_begin)->equals($amounts['recipient_begin']);
            expect('Recipient Debit', $recipient_debit)->equals($amounts['amount']);
            expect('Recipient Credit', $recipient_credit)->equals(0);
            expect('Recipient End Balance', $recipient_end)->equals($amounts['recipient']);
        });

    }

    /**
     * @param $userSender Users
     * @param $userRecipient Users
     * @param $balanceSender string
     * @param $balance2 string
     * @param $user2 string
     */
    private function usersBalance($userSender, $userRecipient, $balanceSender, $balance2, $user2)
    {
        $this->specify('User\'s balance changed', function () use ($userSender, $userRecipient, $balanceSender, $balance2, $user2) {
            $userSender->refresh();
            $userRecipient->refresh();
            expect('Balance of sender', $userSender->balance)->equals($balanceSender);
            expect('Balance of ' . $user2, $userRecipient->balance)->equals($balance2);
        });
    }

    /**
     * @param $user Users
     * @param $amounts array
     */
    private function userFlow($user, $amounts)
    {
        $this->specify('User data flows', function () use ($user, $amounts) {
            $begin = Flows::getBeginTotal($user);
            $debit = Flows::getDebitTotal($user);
            $credit = Flows::getCreditTotal($user);
            $end = Flows::getEndTotal($user);

            expect('User Begin Balance', $begin)->equals($amounts['begin']);
            expect('User Debit', $debit)->equals($amounts['debit']);
            expect('User Credit', $credit)->equals($amounts['credit']);
            expect('User End Balance', $end)->equals($amounts['end']);
        });

    }
}
