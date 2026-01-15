<?php

namespace DealNews\DatoCMS\CMA\Parameters;

use DealNews\DatoCMS\CMA\Parameters\Parts\OrderBy;
use DealNews\DatoCMS\CMA\Parameters\Parts\WebhookCallFilter;

/**
 * Query parameters for listing DatoCMS webhook calls
 *
 * Provides pagination options for the webhook call list API.
 *
 * Usage:
 * ```php
 * $params = new WebhookCall();
 * $params->page->limit = 25;
 * $webhookCalls = $client->webhook_calls->list($params);
 * ```
 *
 * @see https://www.datocms.com/docs/content-management-api/resources/webhook-call
 */
class WebhookCall extends Common {

    /**
     * Attributes to filter
     *
     * @var WebhookCallFilter
     */
    public WebhookCallFilter $filter;

    /**
     * Sort order specification
     *
     * @var OrderBy
     */
    public OrderBy $order_by;

    public function __construct() {
        parent::__construct();
        $this->filter = new WebhookCallFilter();
        $this->order_by = new OrderBy();
    }

    /**
     * Converts parameters to query string array
     *
     * Excludes empty values and formats order_by as comma-separated string.
     *
     * @param array<string, mixed>|null $data Optional data override
     *
     * @return array<string, mixed> Query parameters for API request
     */
    public function toArray(?array $data = null): array {
        $array = parent::toArray($data);
        if ($data === null) {
            if (empty($array['filter'])) {
                unset($array['filter']);
            }
            if (empty($array['order_by'])) {
                unset($array['order_by']);
            } else {
                $array['order_by'] = implode(',', $array['order_by']);
            }
        }
        return $array;
    }
}
