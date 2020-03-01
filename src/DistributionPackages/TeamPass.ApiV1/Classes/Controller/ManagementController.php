<?php

/**
 * NOTICE OF LICENSE
 *
 * This source file is subject to version 3 of the GPL license,
 * that is bundled with this package in the file LICENSE, and is
 * available online at http://www.gnu.org/licenses/gpl.txt
 *
 * PHP version 7
 *
 * @author    Philipp Dittert <philipp.dittert@gmail.com>
 * @copyright 2020 Philipp Dittert <philipp.dittert@gmail.com>
 * @license   http://www.gnu.org/licenses/gpl.txt GNU General Public License, version 3 (GPL-3.0)
 * @link      https://github.com/TeamPass/TeamPass
 */

declare(strict_types=1);

namespace TeamPass\ApiV1\Controller;

use Neos\Flow\Annotations as Flow;
use Neos\Flow\Session\Exception\SessionNotStartedException;
use TeamPass\ApiV1\Service\SettingService;
use TeamPass\Core\Domain\Dto\Person;
use TeamPass\Core\Domain\Dto\Setting;
use TeamPass\Core\Exception\InvalidNewPasswordException;
use TeamPass\Core\Exception\InvalidOldPasswordException;
use TeamPass\Core\Exception\InvalidRepeatPasswordException;
use TeamPass\ApiV1\Service\AclService;
use TeamPass\ApiV1\Service\UserService;

/**
 * Class ManagementController
 *
 * @author    Philipp Dittert <philipp.dittert@gmail.com>
 * @copyright 2020 Philipp Dittert <philipp.dittert@gmail.com>
 * @license   http://www.gnu.org/licenses/gpl.txt GNU General Public License, version 3 (GPL-3.0)
 * @link      https://github.com/TeamPass/TeamPass
 */

class ManagementController extends ProtectedController
{
    /**
     * @Flow\Inject
     * @var AclService
     */
    protected $aclService;

    /**
     * @Flow\Inject
     * @var UserService
     */
    protected $userService;

    /**
     * @Flow\Inject
     * @var SettingService
     */
    protected $settingService;

    /**
     * Returns logged in user settings
     *
     * @return void
     * @throws SessionNotStartedException
     */
    public function getLoggedInUserAction(): void
    {
        $result = [
            "userId"    => $this->session->getData("userId"),
            "userName"  => $this->session->getData("userName"),
            "fullName"  => $this->session->getData("fullName"),
            "isAdmin"   => $this->session->getData("admin")
        ];

        $this->view->assign('value', $result);
    }

    /**
     * initialize changePassword action
     *
     * @return void
     * @throws \Neos\Flow\Mvc\Exception\NoSuchArgumentException
     */
    protected function initializeChangePasswordAction(): void
    {
        $this->abstractInitialize('person', ['password', 'newPassword', 'repeatedNewPassword']);
    }

    /**
     * changes users password if its a local user
     *
     * @param Person $person the person dto
     *
     * @Flow\ValidationGroups({"ManagementControllerChangePasswordAction"})
     * @return void
     */
    public function changePasswordAction(Person $person): void
    {
        try {
            $userId = (int) $this->session->getData("userId");

            $this->userService->changePassword($person, $userId);

            $result = [
                'success' => true,
                'result' => $this->translatorService->trans('MANAGEMENT.CHANGE_PASSWORD_SUCCESS_MSG')
            ];

            $this->view->assign('value', $result);
        } catch (InvalidOldPasswordException $e) {
            $this->response->setStatusCode(400);

            $result = [
                'success' => false,
                'errors' => array(
                    'password' => $e->getMessage()
                )
            ];

            $this->view->assign('value', $result);
        } catch (InvalidNewPasswordException $e) {
            $this->response->setStatusCode(400);

            $result = [
                'success' => false,
                'errors' => array(
                    'newPassword' => $e->getMessage()
                )
            ];

            $this->view->assign('value', $result);
        } catch (InvalidRepeatPasswordException $e) {
            $this->response->setStatusCode(400);

            $result = [
                'success' => false,
                'errors' => array(
                    'repeatedNewPassword' => $e->getMessage()
                )
            ];

            $this->view->assign('value', $result);
        } catch (\Exception $e) {
            // log the exception message
            $this->logger->error($e->getMessage());

            $this->response->setStatusCode(503);

            $result = [
                "success" => false,
                "message" => $this->translatorService->trans('SERVICE.NOT_AVAILABLE')
            ];

            $this->view->assign('value', $result);
        }
    }

    /**
     * @return void
     * @throws \Neos\Flow\Mvc\Exception\NoSuchArgumentException
     */
    protected function initializeChangeLanguageAction(): void
    {
        $this->abstractInitialize('setting', ['language']);
    }

    /**
     * changes users language if its a local user
     *
     * @param Setting $setting
     *
     * @Flow\ValidationGroups({"ManagementControllerChangeLanguageAction"})
     * @return void
     * @throws \Exception
     */
    public function changeLanguageAction(Setting $setting): void
    {
        $userId = (int) $this->session->getData("userId");

        $this->userService->changeLanguage($userId, $setting->getLanguage());

        $response = $this->translatorService->trans(
            'MANAGEMENT.SET_LANGUAGE_SUCCESS_MSG',
            array(
                'langcode' => $setting->getLanguage()
            )
        );

        $result = [
            'success' => true,
            'result' => $response
        ];

        $this->view->assign('value', $result);
    }

    /**
     * returns the language code
     *
     * @return void
     * @throws \Exception
     */
    public function getLanguageAction(): void
    {
        $userId = (int) $this->session->getData("userId");

        $langCode = $this->userService->getLanguage($userId);

        $result = [
            'success' => true,
            'result' => ["langCode" => $langCode]
        ];

        $this->view->assign('value', $result);
    }

    /**
     * @return void
     * @throws \Neos\Flow\Mvc\Exception\NoSuchArgumentException
     */
    protected function initializeChangeThemeAction(): void
    {
        $this->abstractInitialize('setting', ['theme']);
    }

    /**
     * changes users theme
     *
     * @param Setting $setting
     *
     * @Flow\ValidationGroups({"ManagementControllerChangeThemeAction"})
     * @return void
     */
    public function changeThemeAction(Setting $setting): void
    {
        try {
            $userId = (int) $this->session->getData("userId");

            $this->userService->changeTheme($userId, $setting->getTheme());

            $this->view->assign('value', ['success' => true]);
        } catch (\Exception $e) {
            $this->response->setStatusCode(400);
            $this->view->assign('value', ['success' => false, 'message' => $e->getMessage()]);
        }
    }

    /**
     * returns the theme identifier
     *
     * @return void
     * @throws \Exception
     */
    public function getThemeAction(): void
    {
        $userId = (int) $this->session->getData("userId");

        $theme = $this->userService->getTheme($userId);

        $this->view->assign('value', ['success' => true, 'result' => ['theme' => $theme]]);
    }

    /**
     * @return void
     * @throws \Neos\Flow\Mvc\Exception\NoSuchArgumentException
     */
    protected function initializeSetTreeAlphabeticalOrderAction(): void
    {
        $this->abstractInitialize('setting', ['alphabeticalOrder']);
    }

    /**
     * changes users theme
     *
     * @param Setting $setting
     *
     * @Flow\ValidationGroups({"ManagementControllerSetTreeAlphabeticalOrderAction"})
     * @return void
     */
    public function setTreeAlphabeticalOrderAction(Setting $setting): void
    {
        try {
            $userId = (int) $this->session->getData("userId");

            $this->userService->changeTreeAlphabeticalOrder($userId, $setting->isAlphabeticalOrder());

            $this->view->assign('value', ['success' => true]);
        } catch (\Exception $e) {
            $this->response->setStatusCode(400);
            $this->view->assign('value', ['success' => false, 'message' => $e->getMessage()]);
        }
    }

    /**
     * returns flag if tree should be sorted alphabetically
     *
     * @return void
     * @throws \Exception
     */
    public function getTreeAlphabeticalOrderAction(): void
    {
        $userId = (int) $this->session->getData("userId");

        $sort = $this->userService->getTreeAlphabeticalOrder($userId);

        $this->view->assign('value', ['success' => true, 'result' => ['treeAlphabeticalOrder' => $sort]]);
    }

    /**
     * returns the rsa passphrase complexity regex if enabled
     *
     * @return void
     * @throws \Exception
     */
    public function getRsaPassphraseComplexityAction(): void
    {
        // the default return value
        $response = ["enabled" => false];

        if ($this->settingService->get('rsa.passPhrase.forcePassPhraseComplexity') === true) {
            $regex = $this->settingService->get('rsa.passPhrase.passwordRegularExpression');

            if (substr($regex, 0, 1) === "/") {
                $regex = substr($regex, 1);
            }

            if (substr($regex, -1) === "/") {
                $regex = substr($regex, 0, -1);
            }

            $response = [
                'enabled' => true,
                'regex' => $regex
            ];
        }
        $this->view->assign('value', ['success' => true, 'result' => $response]);
    }
}
