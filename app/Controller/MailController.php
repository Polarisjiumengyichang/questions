<?php
declare(strict_types=1);

namespace App\Controller;

use App\Service\MailService;
use Hyperf\Di\Annotation\Inject;

class MailController extends AbstractController
{
    /**
     * @var MailService
     */
    #[Inject]
    protected MailService $mailService;
    public function getCode()
    {
        $email = $this->request->input('email');
        $this->mailService->getCode($email);
        return $this->response->success();
    }
}