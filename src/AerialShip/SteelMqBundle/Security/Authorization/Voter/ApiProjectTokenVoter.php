<?php

namespace AerialShip\SteelMqBundle\Security\Authorization\Voter;

use AerialShip\SteelMqBundle\Entity\Project;
use AerialShip\SteelMqBundle\Entity\ProjectRole;
use AerialShip\SteelMqBundle\Security\Core\Authentication\Token\TokenPreAuthenticatedToken;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;

class ApiProjectTokenVoter implements VoterInterface
{
    /**
     * Checks if the voter supports the given attribute.
     *
     * @param string $attribute An attribute
     *
     * @return bool true if this Voter supports the attribute, false otherwise
     */
    public function supportsAttribute($attribute)
    {
        return ProjectRole::isRoleValid($attribute);
    }

    /**
     * Checks if the voter supports the given class.
     *
     * @param string $class A class name
     *
     * @return bool true if this Voter can process the class
     */
    public function supportsClass($class)
    {
        return $class == 'AerialShip\SteelMqBundle\Entity\Project' ||
            is_subclass_of($class, 'AerialShip\SteelMqBundle\Entity\Project');
    }

    /**
     * Returns the vote for the given parameters.
     *
     * This method must return one of the following constants:
     * ACCESS_GRANTED, ACCESS_DENIED, or ACCESS_ABSTAIN.
     *
     * @param TokenInterface $token      A TokenInterface instance
     * @param object|null    $object     The object to secure
     * @param array          $attributes An array of attributes associated with the method being invoked
     *
     * @return int either ACCESS_GRANTED, ACCESS_ABSTAIN, or ACCESS_DENIED
     */
    public function vote(TokenInterface $token, $object, array $attributes)
    {
        if (false === $token instanceof TokenPreAuthenticatedToken) {
            return VoterInterface::ACCESS_ABSTAIN;
        }
        /** @var TokenPreAuthenticatedToken $token */

        if (false == $object || false == $this->supportsClass(get_class($object))) {
            return VoterInterface::ACCESS_ABSTAIN;
        }

        if (false == $token->getProject()) {
            return VoterInterface::ACCESS_ABSTAIN;
        }

        /** @var Project $project */
        $project = $object;

        if ($project->getId() == $token->getProject()->getId()) {
            // in token is the same project
            return VoterInterface::ACCESS_GRANTED;
        }

        // in token is some another project
        return VoterInterface::ACCESS_DENIED;
    }
}
