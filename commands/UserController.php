<?php

namespace app\commands;

use app\models\User;
use yii\console\Controller;
use yii\console\ExitCode;
use yii\helpers\Console;

/**
 * Manage login users from the command line.
 *
 *   php yii user/set-password alex
 *
 * Prompts for the new password (input is hidden) so it never lands in shell history.
 */
class UserController extends Controller
{
    public function actionSetPassword($username)
    {
        $user = User::findByUsername($username);
        if (!$user) {
            $this->stderr("No user named \"{$username}\".\n", Console::FG_RED);
            return ExitCode::DATAERR;
        }

        $password = $this->promptHidden('New password: ');
        if (strlen($password) < 8) {
            $this->stderr("Password must be at least 8 characters.\n", Console::FG_RED);
            return ExitCode::DATAERR;
        }
        if ($this->promptHidden('Repeat password: ') !== $password) {
            $this->stderr("Passwords do not match.\n", Console::FG_RED);
            return ExitCode::DATAERR;
        }

        $user->setPassword($password);
        $user->updated_at = time();
        if (!$user->save(false, ['password_hash', 'updated_at'])) {
            $this->stderr("Could not save the new password.\n", Console::FG_RED);
            return ExitCode::UNSPECIFIED_ERROR;
        }

        $this->stdout("Password updated for \"{$username}\".\n", Console::FG_GREEN);
        return ExitCode::OK;
    }

    /** @var bool|null whether STDIN is a terminal (checked once, before any read) */
    private $stdinIsTty;

    private function promptHidden($label)
    {
        if ($this->stdinIsTty === null) {
            $this->stdinIsTty = function_exists('stream_isatty') ? stream_isatty(STDIN) : true;
        }
        $interactive = $this->stdinIsTty;

        $this->stdout($label);
        if ($interactive) {
            shell_exec('stty -echo');
        }
        $value = rtrim((string) fgets(STDIN), "\r\n");
        if ($interactive) {
            shell_exec('stty echo');
            $this->stdout("\n");
        }
        return $value;
    }
}
