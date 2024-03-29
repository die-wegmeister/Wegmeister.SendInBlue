<?php
namespace Wegmeister\SendInBlue\Finishers;

/*
 * This file is part of the Wegmeister.Form.FormElements package.
 *
 * (c) Contributors of the Neos Project - www.neos.io
 *
 * This package is Open Source Software. For the full copyright and license
 * information, please view the LICENSE file which was distributed with this
 * source code.
 */

use Neos\Form\Core\Model\AbstractFinisher;
use Neos\Form\Exception\FinisherException;
use Wegmeister\SendInBlue\Exception\Exception;
use Wegmeister\SendInBlue\Service\SendInBlueService;
use Neos\FLow\Annotations as Flow;

/**
 * This finisher adds new contacts to a SendInBlue group
 */
class SendInBlueFinisher extends AbstractFinisher
{

    /**
     * @var SendInBlueService
     * @Flow\Inject
     */
    protected $sendInBlueService;

    /**
     * @return void
     * @throws Exception
     * @throws FinisherException|Exception
     */
    protected function executeInternal()
    {
        $apiKey = $this->parseOption('apiKey');
        $includeListIds = $this->parseOption('includeListIds');
        $templateId = $this->parseOption('templateId');
        $redirectionUrl = $this->parseOption('redirectionUrl');
        $emailIdentifier = $this->parseOption('emailIdentifier');

        if ($apiKey === null || $apiKey === '') {
            throw new FinisherException('The option "apiKey" must be set for the SendInBlueFinisher.', 1603986560);
        } else {
            $this->sendInBlueService->setApiKey($apiKey);
        }
        if (empty($includeListIds)) {
            throw new FinisherException(
                'The option "includeListIds" must be set for the SendInBlueFinisher.',
                1603986637
            );
        }
        if ($templateId === null || $templateId === '') {
            throw new FinisherException('The option "templateId" must be set for the SendInBlueFinisher.', 1603988345);
        }

        if (!is_array($includeListIds)) {
            $includeListIds = [$includeListIds];
        }
        $includeListIds = array_map('intval', $includeListIds);

        $formValues = $this->finisherContext->getFormValues();

        $email = null;
        if ($emailIdentifier === null || $emailIdentifier === '') {
            foreach ($formValues as $identifier => $value) {
                if (preg_match('/^(e-?)?mail$/i', $identifier)) {
                    $emailIdentifier = $identifier;
                    break;
                }
            }
            if ($emailIdentifier === null || $emailIdentifier === '') {
                throw new FinisherException(
                    'The required field for mails could not be determined. '
                        . 'Either fill the field "emailIdentifier" with the matching identifier of '
                        . 'the email field or change the identifier of the email field to "E-Mail".',
                    1603987934
                );
            }
        }
        if (!isset($formValues[$emailIdentifier])) {
            throw new FinisherException(
                'The option "emailIdentifier" does not match a field of the form.',
                1603988116
            );
        }
        $email = $formValues[$emailIdentifier];
        unset($formValues[$emailIdentifier]);

        $formRuntime = $this->finisherContext->getFormRuntime();
        $referrer = $formRuntime->getRequest()->getHttpRequest()->getUri()->__toString();

        $this->sendInBlueService->createDoiContact(
            $email,
            $formValues,
            $includeListIds,
            (int)$templateId,
            $redirectionUrl ?: $referrer
        );
    }
}
