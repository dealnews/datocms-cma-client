<?php

namespace DealNews\DatoCMS\CMA\Exception;

/**
 * Exception thrown when job polling times out
 *
 * Thrown by Upload API helper methods when waiting for a background job to
 * complete exceeds the specified timeout. The job ID and elapsed time are
 * stored for debugging purposes.
 *
 * Usage:
 * ```php
 * try {
 *     $upload = $client->upload->uploadFileAndWait(
 *         '/path/to/file.jpg',
 *         timeout: 30
 *     );
 * } catch (Timeout $e) {
 *     echo "Timeout after " . $e->getElapsedTime() . " seconds";
 *     echo "Job ID: " . $e->getJobId();
 * }
 * ```
 *
 * @see \DealNews\DatoCMS\CMA\API\Upload::uploadFileAndWait()
 * @see \DealNews\DatoCMS\CMA\API\Upload::uploadFromUrlAndWait()
 */
class Timeout extends \RuntimeException {

    /**
     * Job ID that timed out
     *
     * @var string
     */
    protected string $job_id;

    /**
     * Elapsed time in seconds
     *
     * @var float
     */
    protected float $elapsed_time;

    /**
     * Creates a new timeout exception
     *
     * @param string          $message      Exception message
     * @param int             $code         Exception code
     * @param string          $job_id       Job ID that timed out
     * @param float           $elapsed_time Elapsed time in seconds
     * @param \Throwable|null $previous     Previous exception
     */
    public function __construct(
        string $message,
        int $code = 0,
        string $job_id = '',
        float $elapsed_time = 0.0,
        ?\Throwable $previous = null
    ) {
        $this->job_id       = $job_id;
        $this->elapsed_time = $elapsed_time;
        parent::__construct($message, $code, $previous);
    }

    /**
     * Returns the job ID that timed out
     *
     * @return string Job ID
     */
    public function getJobId(): string {
        return $this->job_id;
    }

    /**
     * Returns the elapsed time in seconds
     *
     * @return float Elapsed time
     */
    public function getElapsedTime(): float {
        return $this->elapsed_time;
    }
}
