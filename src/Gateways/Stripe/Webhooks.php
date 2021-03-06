<?php

namespace Shoperti\PayMe\Gateways\Stripe;

use BadMethodCallException;
use InvalidArgumentException;
use Shoperti\PayMe\Contracts\WebhookInterface;
use Shoperti\PayMe\Gateways\AbstractApi;

/**
 * This is the webhooks class.
 *
 * @author Arturo Rodríguez <arturo.rodriguez@dinkbit.com>
 */
class Webhooks extends AbstractApi implements WebhookInterface
{
    /**
     * Get all webhooks.
     *
     * @param array $params
     *
     * @return \Shoperti\PayMe\Contracts\ResponseInterface
     */
    public function all($params = [])
    {
        return $this->gateway->commit('get', $this->gateway->buildUrlFromString('webhook_endpoints'));
    }

    /**
     * Find a webhook by its id.
     *
     * @param int|string $id
     * @param array      $params
     *
     * @return \Shoperti\PayMe\Contracts\ResponseInterface
     */
    public function find($id = null, $params = [])
    {
        if (!$id) {
            throw new InvalidArgumentException('We need an id');
        }

        return $this->gateway->commit('get', $this->gateway->buildUrlFromString('webhook_endpoints/'.$id));
    }

    /**
     * Create a webhook.
     *
     * @param array $params
     *
     * @return \Shoperti\PayMe\Contracts\ResponseInterface
     */
    public function create($params = [])
    {
        return $this->gateway->commit('post', $this->gateway->buildUrlFromString('webhook_endpoints'), $params);
    }

    /**
     * Update a webhook.
     *
     * @param array $params
     *
     * @return \Shoperti\PayMe\Contracts\ResponseInterface
     */
    public function update($params = [])
    {
        throw new BadMethodCallException();
    }

    /**
     * Delete a webhook.
     *
     * @param int|string $id
     * @param array      $params
     *
     * @return \Shoperti\PayMe\Contracts\ResponseInterface
     */
    public function delete($id, $params = [])
    {
        return $this->gateway->commit('delete', $this->gateway->buildUrlFromString('webhook_endpoints/'.$id));
    }
}
