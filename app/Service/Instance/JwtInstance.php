<?php
declare(strict_types=1);

namespace App\Service\Instance;

use App\Model\User;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use App\Constants\ErrorCode;
use App\Service\UserService;
use Firebase\JWT\ExpiredException;
use App\Exception\BusinessException;
use Hyperf\Context\ApplicationContext;

class JwtInstance extends Instance
{
    public const KEY = 'questions';
    public const ALG = 'HS256';
    public const EXPIRE_TIME = 3600 * 24 * 30;
    public ?int $id = null;
    public ?array $user = null;

    public function encode(User $user)
    {
        $this->id = $user->id;
        return JWT::encode(['id' => $user->id, 'exp' => time() + self::EXPIRE_TIME], self::KEY, self::ALG);
    }

    public function decode(string $token): self
    {
        try {
            $decoded = (array) JWT::decode($token, new Key(self::KEY, self::ALG));
        } catch (ExpiredException $exception) {
            throw new BusinessException(ErrorCode::TOKEN_EXPIRED);
        } catch (\Throwable $exception) {
            throw new BusinessException(ErrorCode::SERVER_ERROR, $exception->getMessage());
        }
        if ($id = $decoded['id']) {
            $this->id = $id;
            $this->user = $this->getUser();
        }
        return $this;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?array
    {
    
        if ($this->user === null) {
            if (! empty($id = $this->getId())) {
                $this->user = ApplicationContext::getContainer()->get(UserService::class)->getUserInfoFromCache($id);
            }
        }

      
        return $this->user;
    }
}