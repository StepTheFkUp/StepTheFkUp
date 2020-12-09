<?php

declare(strict_types=1);

namespace EonX\EasyBankFiles\Parsers\DirectEntry;

use EonX\EasyBankFiles\Parsers\AbstractLineByLineParser;
use EonX\EasyBankFiles\Parsers\DirectEntry\Results\Header;
use EonX\EasyBankFiles\Parsers\DirectEntry\Results\Trailer;
use EonX\EasyBankFiles\Parsers\DirectEntry\Results\Transaction;
use EonX\EasyBankFiles\Parsers\Error;

final class Parser extends AbstractLineByLineParser
{
    /**
     * @var string Code for header line
     */
    private const HEADER = '0';

    /**
     * @var int Minimal header line length (calculated from maximum substr arguments)
     */
    private const MIN_HEADER_LINE_LENGTH = 80;

    /**
     * @var int Minimal trailer line length (calculated from maximum substr arguments)
     */
    private const MIN_TRAILER_LINE_LENGTH = 80;

    /**
     * @var int Minimal transaction line length (calculated from maximum substr arguments)
     */
    private const MIN_TRANSACTION_LINE_LENGTH = 120;

    /**
     * @var string Code for trailer line
     */
    private const TRAILER = '7';

    /**
     * @var string Code for transaction
     */
    private const TRANSACTION_1 = '1';

    /**
     * @var string Code for transaction
     */
    private const TRANSACTION_2 = '2';

    /**
     * @var mixed[] $errors
     */
    private $errors = [];

    /**
     * @var \EonX\EasyBankFiles\Parsers\DirectEntry\Results\Header
     */
    private $header;

    /**
     * @var \EonX\EasyBankFiles\Parsers\DirectEntry\Results\Trailer
     */
    private $trailer;

    /**
     * @var \EonX\EasyBankFiles\Parsers\DirectEntry\Results\Transaction[]
     */
    private $transactions;

    /**
     * @return \EonX\EasyBankFiles\Parsers\Error[]
     */
    public function getErrors(): array
    {
        return $this->errors;
    }

    /**
     * Get header record.
     */
    public function getHeader(): Header
    {
        return $this->header;
    }

    /**
     * Get trailer record.
     */
    public function getTrailer(): Trailer
    {
        return $this->trailer;
    }

    /**
     * @return \EonX\EasyBankFiles\Parsers\DirectEntry\Results\Transaction[]
     */
    public function getTransactions(): array
    {
        return $this->transactions;
    }

    /**
     * {@inheritDoc}
     */
    protected function processLine(int $lineNumber, string $line): void
    {
        // code is the first character in line
        $code = $line[0];
        $lineLength = \strlen($line);

        if ($code === self::HEADER && $lineLength >= self::MIN_HEADER_LINE_LENGTH) {
            $this->header = $this->processHeader($line);

            return;
        }

        if ($code === self::TRAILER && $lineLength >= self::MIN_TRAILER_LINE_LENGTH) {
            $this->trailer = $this->processTrailer($line);

            return;
        }

        if (
            ($code === self::TRANSACTION_1 || $code === self::TRANSACTION_2) &&
            $lineLength >= self::MIN_TRANSACTION_LINE_LENGTH
        ) {
            $this->transactions[] = $this->processTransaction($line);

            return;
        }

        $this->errors[] = new Error(\compact('line', 'lineNumber'));
    }

    /**
     * Process header block of line.
     */
    private function processHeader(string $line): Header
    {
        $dateProcessed = \substr($line, 74, 6);
        $description = \substr($line, 62, 12);
        $userFinancialInstitution = \substr($line, 20, 3);
        $userIdSupplyingFile = \substr($line, 56, 6);
        $userSupplyingFile = \substr($line, 30, 26);
        $reelSequenceNumber = \substr($line, 18, 2);

        return new Header([
            'dateProcessed' => $dateProcessed === false ? null : $dateProcessed,
            'description' => $description === false ? null : \trim($description),
            'userFinancialInstitution' => $userFinancialInstitution === false ? null : $userFinancialInstitution,
            'userIdSupplyingFile' => $userIdSupplyingFile === false ? null : $userIdSupplyingFile,
            'userSupplyingFile' => $userSupplyingFile === false ? null : \trim($userSupplyingFile),
            'reelSequenceNumber' => $reelSequenceNumber === false ? null : $reelSequenceNumber,
        ]);
    }

    /**
     * Process trailer block of line.
     */
    private function processTrailer(string $line): Trailer
    {
        $bsb = \substr($line, 1, 7);
        $numberPayments = \substr($line, 74, 6);
        $totalNetAmount = \substr($line, 20, 10);
        $totalCreditAmount = \substr($line, 30, 10);
        $totalDebitAmount = \substr($line, 40, 10);

        return new Trailer([
            'bsb' => $bsb === false ? null : \str_replace('-', '', $bsb),
            'numberPayments' => $numberPayments === false ? null : $this->trimLeftZeros($numberPayments),
            'totalNetAmount' => $totalNetAmount === false ? null : $this->trimLeftZeros($totalNetAmount),
            'totalCreditAmount' => $totalCreditAmount === false ? null : $this->trimLeftZeros($totalCreditAmount),
            'totalDebitAmount' => $totalDebitAmount === false ? null : $this->trimLeftZeros($totalDebitAmount),
        ]);
    }

    /**
     * Process transaction block of line.
     */
    private function processTransaction(string $line): Transaction
    {
        $accountName = \substr($line, 30, 32);
        $accountNumber = \substr($line, 8, 9);
        $amount = \substr($line, 20, 10);
        $bsb = \substr($line, 1, 7);
        $lodgmentReference = \substr($line, 62, 18);
        $remitterName = \substr($line, 96, 16);
        $traceAccountNumber = \substr($line, 87, 9);
        $traceBsb = \substr($line, 80, 7);
        $txnCode = \substr($line, 18, 2);
        $withholdingTax = \substr($line, 112, 8);

        return new Transaction([
            'accountName' => $accountName === false ? null : \trim($accountName),
            'accountNumber' => $accountNumber === false ? null : $accountNumber,
            'amount' => $amount === false ? null : $this->trimLeftZeros($amount),
            'bsb' => $bsb === false ? null : \str_replace('-', '', $bsb),
            'indicator' => $line[17] ?? '',
            'lodgmentReference' => $lodgmentReference === false ? null : \trim($lodgmentReference),
            'recordType' => $line[0] ?? '',
            'remitterName' => $remitterName === false ? null : \trim($remitterName),
            'traceAccountNumber' => $traceAccountNumber === false ? null : $traceAccountNumber,
            'traceBsb' => $traceBsb === false ? null : \str_replace('-', '', $traceBsb),
            'txnCode' => $txnCode === false ? null : $txnCode,
            'withholdingTax' => $withholdingTax === false ? null : $withholdingTax,
        ]);
    }
}
