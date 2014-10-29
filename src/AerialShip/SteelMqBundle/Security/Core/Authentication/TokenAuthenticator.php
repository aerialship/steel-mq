<?php

namespace AerialShip\SteelMqBundle\Security\Core\Authentication;

use AerialShip\SteelMqBundle\Entity\ProjectRoleRepository;
use AerialShip\SteelMqBundle\Entity\UserRepository;
use AerialShip\SteelMqBundle\Security\Core\Authentication\Token\TokenPreAuthenticatedToken;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\SimplePreAuthenticatorInterface;
use Symfony\Component\Security\Core\Authentication\Token\PreAuthenticatedToken;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\User\UserProviderInterface;

class TokenAuthenticator implements SimplePreAuthenticatorInterface
{
    /** @var ProjectRoleRepository */
    protected $projectRoleRepository;

    /** @var UserRepository */
    protected $userRepository;

    /**
     * @param ProjectRoleRepository $projectRoleRepository
     * @param UserRepository $userRepository
     */
    public function __construct(ProjectRoleRepository $projectRoleRepository, UserRepository $userRepository)
    {
        $this->projectRoleRepository = $projectRoleRepository;
        $this->userRepository = $userRepository;
    }

    /**
     * @param Request $request
     * @param string $providerKey
     * @return PreAuthenticatedToken
     * @throws \Symfony\Component\Security\Core\Exception\BadCredentialsException
     */
    public function createToken(Request $request, $providerKey)
    {
        $accessToken = null;
        if ($request->headers->has('Authorization')) {
            if (preg_match('/token (?P<token>.*)/', $request->headers->get('Authorization'), $matches)) {
                $accessToken = $matches['token'];
            }
        }
        if (false == $accessToken) {
            $accessToken = $request->request->get('token');
        }
        if (false == $accessToken) {
            $accessToken = $request->query->get('token');
        }

        if (false == $accessToken) {
            return null;
        }

        return new TokenPreAuthenticatedToken(
            'anon.',
            null,
            $accessToken,
            $providerKey
        );
    }

    /**
     * @param TokenInterface $token
     * @param UserProviderInterface $userProvider
     * @param string $providerKey
     * @return PreAuthenticatedToken
     * @throws \Symfony\Component\Security\Core\Exception\AuthenticationException
     */
    public function authenticateToken(TokenInterface $token, UserProviderInterface $userProvider, $providerKey)
    {
        $accessToken = $token->getCredentials();

        $projectRole = $this->projectRoleRepository->getByAccessToken($accessToken);

        if ($projectRole) {
            $user = $projectRole->getUser();
        } else {
            $user = $this->userRepository->getByAccessToken($accessToken);
        }

        if (false == $user) {
            return new TokenPreAuthenticatedToken(
                'anon.',
                null,
                $accessToken,
                $providerKey
            );
        }

        /** @noinspection PhpParamsInspection */

        return new TokenPreAuthenticatedToken(
            $user,
            $projectRole ? $projectRole->getProject() : null,
            $accessToken,
            $providerKey,
            $user->getRoles()
        );
    }

    /**
     * @param TokenInterface $token
     * @param string $providerKey
     * @return bool
     */
    public function supportsToken(TokenInterface $token, $providerKey)
    {
        return $token instanceof TokenPreAuthenticatedToken && $token->getProviderKey() === $providerKey;
    }

}
