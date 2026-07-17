<?php
namespace Pes\Mail;

/**
 *
 * @author pes2704
 */
interface MailInterface {
    public static function actionOnSend(bool $result, array $to, array $cc, array $bcc, string $subject, string $body, string $from, array $extra): void;
        public function mail(?AssemblyInterface $params = null): bool;
    
}
