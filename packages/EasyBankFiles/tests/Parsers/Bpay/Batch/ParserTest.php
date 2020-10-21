<?php

declare(strict_types=1);

namespace EonX\EasyBankFiles\Tests\Parsers\Bpay\Batch;

use EonX\EasyBankFiles\Parsers\Bpay\Batch\Parser;
use EonX\EasyBankFiles\Tests\Parsers\TestCase;

final class ParserTest extends TestCase
{
    /**
     * Should return error from the content
     *
     * @group Batch-Parser-Error
     */
    public function testShouldReturnErrors(): void
    {
        $batchParser = new Parser('invalid');

        self::assertIsArray($batchParser->getErrors());
    }

    /**
     * Should return Header object
     *
     * @group Batch-Parser-Header
     */
    public function testShouldReturnHeader(): void
    {
        $batchParser = new Parser($this->getSampleFileContents('sample.BPB'));

        $header = $batchParser->getHeader();
        self::assertSame('101249          ', $header->getCustomerId());
        self::assertSame('CustomerShortName   ', $header->getCustomerShortName());
        self::assertSame('20190717', $header->getProcessingDate());
        self::assertSame('', $header->getRestOfRecord());
    }

    /**
     * Should return trailer from the content
     *
     * @group Batch-Parser-Trailer
     */
    public function testShouldReturnTrailer(): void
    {
        $batchParser = new Parser($this->getSampleFileContents('sample.BPB'));

        $trailer = $batchParser->getTrailer();
        self::assertSame('0000000000342', $trailer->getAmountOfApprovals());
        self::assertSame('0000000000000', $trailer->getAmountOfDeclines());
        self::assertSame('0000000000342', $trailer->getAmountOfPayments());
        self::assertSame('0000000002', $trailer->getNumberOfApprovals());
        self::assertSame('0000000000', $trailer->getNumberOfDeclines());
        self::assertSame('0000000002', $trailer->getNumberOfPayments());
        self::assertSame('', $trailer->getRestOfRecord());
    }

    /**
     * Should return Transaction and TransactionItem class
     *
     * @group Batch-Parser-Transaction
     */
    public function testShouldReturnTransaction(): void
    {
        $batchParser = new Parser($this->getSampleFileContents('sample.BPB'));

        $transactions = $batchParser->getTransactions();

        self::assertIsArray($transactions);
        self::assertCount(2, $transactions);

        $firstTransactionItem = $transactions[0];
        self::assertSame('0000000000162', $firstTransactionItem->getAmount());
        self::assertSame('083170', $firstTransactionItem->getAccountBsb());
        self::assertSame('739813974', $firstTransactionItem->getAccountNumber());
        self::assertSame('0000254177', $firstTransactionItem->getBillerCode());
        self::assertSame('1444089773          ', $firstTransactionItem->getCustomerReferenceNumber());
        self::assertSame('          ', $firstTransactionItem->getReference1());
        self::assertSame('                    ', $firstTransactionItem->getReference2());
        self::assertSame('                                                  ', $firstTransactionItem->getReference3());
        self::assertSame('', $firstTransactionItem->getRestOfRecord());
        self::assertSame('0000', $firstTransactionItem->getReturnCode());
        self::assertSame(
            'PROCESSED                                         ',
            $firstTransactionItem->getReturnCodeDescription()
        );
        self::assertSame('NAB201907175132940001', $firstTransactionItem->getTransactionReferenceNumber());
    }

    /**
     * Get sample file contents.
     */
    private function getSampleFileContents(string $file): string
    {
        return \file_get_contents(\realpath(__DIR__) . '/data/' . $file) ?: '';
    }
}
