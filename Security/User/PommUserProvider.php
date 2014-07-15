<?php

namespace Pomm\PommBundle\Security\User;

use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;

/**
 * Provides easy to use provisioning for Pomm model users.
 *
 * @author Arnaud BUCHOUX <arnaud.buchoux@gmail.com>
 */
class PommUserProvider implements UserProviderInterface
{
    /**
     * A Pomm service
     *
     * @var \Pomm\Service
     */
    protected $pomm;

    /**
     * A Model class name.
     *
     * @var string
     */
    protected $class;

    /**
     * A property to use to retrieve the user.
     *
     * @var string
     */
    protected $property;

    /**
     * A database to use to retrieve the user.
     *
     * @var string
     */
    protected $database;

    /**
     * Default constructor
     *
     * @param \Pomm\Service $pomm          The Pomm service.
     * @param string        $class         The User model class.
     * @param string        $property|null The property to use to retrieve a user.
     * @param string        $database|null The database to use to retrieve a user.
     */
    public function __construct(\Pomm\Service $pomm, $class, $property = null, $database = null)
    {
        $this->pomm = $pomm;
        $this->class = $class;
        $this->property = $property;
        $this->database = $database;
    }

    /**
     * {@inheritdoc}
     */
    public function loadUserByUsername($username)
    {
        $map = $this->pomm
                    ->getDatabase($this->database)
                    ->createConnection()
                    ->getMapFor($this->class);

        $users = $map->findWhere(
            sprintf('%s = $*', (null !== $this->property) ? $this->property : 'username'),
            array($username),
            'LIMIT 1'
        );

        if (0 == count($users)) {
            throw new UsernameNotFoundException(
                sprintf('Username "%s" does not exist.', $username)
            );
        }

        return $users->current();
    }

    /**
     * {@inheritdoc}
     */
    public function refreshUser(UserInterface $user)
    {
        if (!$user instanceof $this->class) {
            throw new UnsupportedUserException(
                sprintf('Instances of "%s" are not supported.', get_class($user))
            );
        }

        return $this->loadUserByUsername($user->getUsername());
    }

    /**
     * {@inheritdoc}
     */
    public function supportsClass($class)
    {
        return $class === $this->class;
    }
}
