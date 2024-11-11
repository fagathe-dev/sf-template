<?php
namespace App\Service;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Exception\ORMException;
use Exception;
use Fagathe\Phplib\Helpers\DateTimeTrait;
use Fagathe\Phplib\Helpers\ResponseTrait;
use Fagathe\Phplib\Service\Breadcrumb\Breadcrumb;
use Fagathe\Phplib\Service\Breadcrumb\BreadcrumbItem;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

final class UserService
{

    use ResponseTrait;
    use DateTimeTrait;

    public function __construct(
        private UserRepository $repository,
        private PaginatorInterface $paginator,
        private UserPasswordHasherInterface $hasher,
        private LoggerInterface $logger,
        private EntityManagerInterface $manager,
        private Security $security
    ) {
    }

    /**
     * update
     *
     * @param  mixed $user
     * @return bool
     */
    public function update(User $user): bool
    {
        $user->setUpdatedAt($this->now());
        $result = $this->save($user);

        if ($result) {
            $this->addFlash('success', 'Utilisateur enregistré 🚀');
        } else {
            $this->addFlash('danger', 'Une erreur est survenue lors de l\'enregistrement de ce compte !');
        }

        return $result;
    }

    /**
     * hash
     *
     * @param  mixed $user
     * @return User
     */
    private function hash(User $user): User
    {
        return $user->setPassword(
            $this->hasher->hashPassword($user, $user->getPassword())
        );
    }

    /**
     * create
     *
     * @param  mixed $user
     * @return bool
     */
    public function create(User $user): bool
    {
        $user->setCreatedAt($this->now())
            ->setConfirm(true);
        $this->hash($user);

        $result = $this->save($user);

        if ($result) {
            $this->addFlash('success', 'Utilisateur crée 🚀');
        } else {
            $this->addFlash('danger', 'Une erreur est survenue lors de l\'enregistrement de ce compte !');
        }

        return $result;
    }

    /**
     * save
     *
     * @param  User $user
     * @return bool
     */
    public function save(User $user): bool
    {
        try {
            $this->manager->persist($user);
            $this->manager->flush();
            return true;
        } catch (ORMException $e) {
            $this->logger->error($e->getMessage());
            $this->addFlash('danger', $e->getMessage());
            return false;
        } catch (Exception $e) {
            $this->addFlash('danger', $e->getMessage());
            $this->logger->error($e->getMessage());
            return false;
        }
    }

    /**
     * remove
     *
     * @param  User $object
     * @return object|bool
     */
    public function remove(User $user): bool|object
    {
        try {
            $this->manager->remove($user);
            $this->manager->flush();
            $this->logger->info('User {username} is removed form db', ['username' => $user->getUsername()]);
            return $this->sendNoContent();
        } catch (ORMException $e) {
            $this->addFlash('danger', 'Une erreur est survenue lors de la suppression de votre compte !');
            $this->logger->error($e->getMessage());
            return false;
        } catch (Exception $e) {
            $this->addFlash('danger', $e->getMessage());
            $this->logger->error($e->getMessage());
            return false;
        }
    }

    public function updatePassword(string $plainPassword, User $user): bool
    {
        $user->setPassword(
            $this->hasher->hashPassword($user, $plainPassword)
        );



        return $this->update($user);
    }

    /**
     * @param  mixed $request
     * @return PaginationInterface
     */
    public function getUsers(Request $request): PaginationInterface
    {

        $data = $this->repository->findAll(); #findUsersAdmin();
        $page = $request->query->getInt('page', 1);
        $nbItems = $request->query->getInt('nbItems', 15);

        return $this->paginator->paginate(
            $data,
            /* query NOT result */
            $page,
            /*page number*/
            $nbItems, /*limit per page*/
        );
    }

    /**
     * index
     *
     * @param  mixed $request
     * @return array
     */
    public function index(Request $request): array
    {
        $breadcrumb = new Breadcrumb([
            new BreadcrumbItem('Liste des utilisateurs'),
        ]);

        $paginatedUsers = $this->getUsers($request);

        return compact('paginatedUsers', 'breadcrumb');
    }

    /**
     * get logged User
     *
     * @return User
     */
    private function getUser(): ?User
    {
        $user = $this->security->getUser();

        if ($user instanceof User) {
            return $user;
            
        }
        return null;
    }

}