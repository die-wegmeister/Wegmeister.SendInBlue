<?php
namespace Wegmeister\SendInBlue\Service;

/*
 * This file is part of the Wegmeister.Form.FormElements package.
 *
 * (c) Contributors of the Neos Project - www.neos.io
 *
 * This package is Open Source Software. For the full copyright and license
 * information, please view the LICENSE file which was distributed with this
 * source code.
 */

use SendinBlue\Client\Api\ContactsApi;
use SendinBlue\Client\Configuration as SendinBlueConfiguration;
use SendinBlue\Client\Model\CreateDoiContact;
use Wegmeister\SendInBlue\Exception\Exception;

class SendInBlueService
{

    /**
     * @var \SendinBlue\Client\Api\ContactsApi
     */
    protected $contactsApi;

    protected string $apiKey;

    public function __construct(string $apiKey)
    {
        $this->setApiKey($apiKey);
    }

    public function setApiKey(string $apiKey): void
    {
        $config = SendinBlueConfiguration::getDefaultConfiguration()->setApiKey('api-key', $apiKey);
        $this->contactsApi = new ContactsApi(
            new \GuzzleHttp\Client(),
            $config
        );
    }

    /**
     * @param string $email Email address where the confirmation email will be sent. This email address will be the identifier for all other contact attributes.
     * @param array $attributes Pass the set of attributes and their values. These attributes must be present in your SendinBlue account. For eg. {'FNAME':'Elly', 'LNAME':'Roger'}
     * @param array $includeListIds Lists under user account where contact should be added
     * @param int $templateId ID of the Double opt-in (DOI) template
     * @param string $redirectionUrl URL of the web page that user will be redirected to after clicking on the double opt in URL. When editing your DOI template you can reference this URL by using the tag {{ params.DOIurl }}.
     * @throws Exception
     */
    public function createDoiContact(
        string $email,
        array $attributes,
        array $includeListIds,
        int $templateId,
        string $redirectionUrl
    ): void
    {
        $newContact = new CreateDoiContact([
            'email' => $email,
            'attributes' => empty($attributes) ? null: $attributes,
            'includeListIds' => $includeListIds,
            'templateId' => $templateId,
            'redirectionUrl' => $redirectionUrl
        ]);

        try {
            $this->contactsApi->createDoiContact($newContact);
        } catch (\Exception $e) {
            throw new Exception(
                'Could not create new SendInBlue contact. API-Exception: ' . $e->getMessage(),
                1680711227
            );
        }
    }
}