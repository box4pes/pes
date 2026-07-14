<?php
namespace Pes\Mail;

/**
 *
 * @author pes2704
 */
interface MailInterface {
    public static function actionOnSend(bool $result, array $to, array $cc, array $bcc, string $subject, string $body, string $from, array $extra);
        public function mail(AssemblyInterface $params = null): bool;
    
}
