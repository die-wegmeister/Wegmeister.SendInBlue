<?php
namespace Wegmeister\SendInBlue\FusionForm\Action;

/*
 * This file is part of the Wegmeister.Form.FormElements package.
 *
 * (c) Contributors of the Neos Project - www.neos.io
 *
 * This package is Open Source Software. For the full copyright and license
 * information, please view the LICENSE file which was distributed with this
 * source code.
 */

use Neos\Flow\Mvc\ActionResponse;
use Neos\Fusion\Form\Runtime\Action\AbstractAction;
use Neos\Fusion\Form\Runtime\Domain\Exception\ActionException;
use Wegmeister\SendInBlue\Service\SendInBlueService;

class SendInBlueAction extends AbstractAction
{

    /**
     * @var SendInBlueService
     * @Flow\Inject
     */
    protected $sendInBlueService;

    protected $options = [
        'apiKey' => '',
        'includeListIds' => [],
        'templateId' => null,
        'redirectionUrl' => '',
        'email' => '',
        'attributes' => []
    ];

    public function perform(): ?ActionResponse
    {
        $apiKey = $this->options['apiKey'] ?? null;
        $includeListIds = $this->options['includeListIds'];
        $templateId = $this->options['templateId'];
        $redirectionUrl = $this->options['redirectionUrl'];
        $email = $this->options['email'];
        $attributes = $this->options['attributes'] ?? [];

        if ($apiKey === null || $apiKey === '') {
            throw new ActionException(
                'The option "apiKey" must be set for SendInBlueAction.',
                1680709673
            );
        } else {
            $this->sendInBlueService->setApiKey($apiKey);
        }
        if (empty($includeListIds)) {
            throw new ActionException(
                'The option "includeListIds" must be set for the SendInBlueAction.',
                1680709674
            );
        }
        if (!is_int($templateId)) {
            throw new ActionException(
                'The option "templateId" must be set with integer value for the SendInBlueAction.',
                1680709675
            );
        }
        if (!is_array($includeListIds)) {
            $includeListIds = [$includeListIds];
        }
        $includeListIds = array_map('intval', $includeListIds);

        $this->sendInBlueService->createDoiContact(
            $email,
            $attributes,
            $includeListIds,
            (int)$templateId,
            $redirectionUrl
        );

        return null;
    }
}