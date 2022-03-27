<?php declare(strict_types=1);

namespace App\Service;

use App\Entity\Record;
use App\Repository\RecordRepository;
use Psr\Log\LoggerInterface;

class RecordService
{
    private RecordRepository $repository;
    private LoggerInterface $logger;

    public function __construct(RecordRepository $repository, LoggerInterface $logger)
    {
        $this->repository = $repository;
        $this->logger = $logger;
    }

    /**
     * Get wallet records
     *
     * @param string $walletId
     *
     * @return Record[]
     */
    public function getWalletRecords(string $walletId): array
    {
        return $this->repository->getWalletRecords($walletId);
    }
}