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
use Neos\Flow\Http\Cookie;
use Neos\Flow\Session\Exception\SessionNotStartedException;
use TeamPass\ApiV1\Service\SettingService;
use TeamPass\Core\Domain\Model\User;
use TeamPass\Core\Domain\Dto\Person;
use TeamPass\ApiV1\Service\AclService;
use TeamPass\ApiV1\Service\AuthService;
use TeamPass\ApiV1\Service\UserService;
use TeamPass\Core\Util\Keys;

/**
 * Class AuthController
 *
 * @author    Philipp Dittert <philipp.dittert@gmail.com>
 * @copyright 2020 Philipp Dittert <philipp.dittert@gmail.com>
 * @license   http://www.gnu.org/licenses/gpl.txt GNU General Public License, version 3 (GPL-3.0)
 * @link      https://github.com/TeamPass/TeamPass
 *
 * @Flow\Scope("singleton")
 */

class AuthController extends AbstractController
{
    /**
     * @Flow\Inject
     * @var AuthService
     */
    protected $authService;

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
     *
     * @return void
     * @throws \Neos\Flow\Mvc\Exception\NoSuchArgumentException
     */
    public function initializeLoginAction()
    {
        $this->abstractInitialize('person', ["userName", "password", "language"]);
    }

    /**
     * try to login with given user credentials
     *
     * @param Person $person the Person value object
     *
     * @Flow\ValidationGroups({"AuthControllerLoginAction"})
     * @return void
     */
    public function loginAction(Person $person): void
    {
        try {
            $session = $this->session;

            // try to login with credentials
            /** @var User $user */
            $user = $this->authService->login($person);

            if ($session->isStarted()) {
                $session->destroy();
            }

            $session->start();

            // find the current selected application language
            if ($person->getLanguage() !== null  && $person->getLanguage() !== "default") {
                $language = $person->getLanguage();
            } else {
                if (is_null($user->getLanguage())) {
                    $language = Keys::DEFAULT_LANGUAGE;
                } else {
                    $language = $user->getLanguage();
                }
            }

            if (!$session->getData("userId")) {
                $session->putData("userName", $user->getUsername());
                $session->putData("fullName", $user->getFullName());
                $session->putData("userId", $user->getUserId());
                $session->putData("presetLanguage", $user->getLanguage());
                $session->putData("language", $language);
            } else {
                // even if the session already exists, we override some values to ensure, the user settings are
                // getting published correctly
                $session->putData("fullName", $user->getFullName());
                $session->putData("language", $language);
            }

            $result["userId"] = $user->getUserId();
            $result["userName"] = $user->getUsername();
            $result["fullName"] = $user->getFullName();
            $result["presetLanguage"] = $user->getLanguage();
            $result["isLocal"] = $this->userService->isLocalUser($user->getUserId());


            if ($this->settingService->get('pollInterval') !== null) {
                $result["pollInterval"] = $this->settingService->get('pollInterval');
            } else {
                $result["pollInterval"] = Keys::DEFAULT_POLL_INTERVAL;
            }

            // query if user is admin in non strict mode (no exception, only (bool)false if no admin)
            $result["isAdmin"] = $this->aclService->isAdmin($user->getUserId(), false);

            $this->view->assign('value', ['success' => true, 'result' => $result]);

            $randomKey = $this->generateRandomKey();
            $this->response->setCookie(
                new Cookie(
                    'identifier',
                    $randomKey,
                    0,
                    null,
                    null,
                    "/",
                    false,
                    false
                )
            );

            $session->putData("identifier", $randomKey);
        } catch (\Exception $e) {
            $this->logger->info($e->getMessage());

            // delay login
            sleep(1);

            $this->response->setStatusCode(403);

            $result = [
                'success' => false,
                'message' =>  [
                    'userName' => $this->translatorService->trans('AUTH.INVALID_USERNAME'),
                    'password' => $this->translatorService->trans('AUTH.INVALID_PASSWORD')
                ]
            ];

            $this->view->assign('value', $result);
        }
    }

    /**
     * Logout action destroys session
     *
     * @return void
     * @throws SessionNotStartedException
     */
    public function logoutAction(): void
    {
        $session = $this->session;

        if ($session->isStarted()) {
            $session->destroy();

            $this->view->assign('value', ["success" => true]);
        }

        $this->view->assign('value', ["success" => false]);
    }
}
