<?php
namespace App\Entity;

use App\Entity\LeaveEntitlement;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\AdvancedUserInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity()
 * @UniqueEntity(fields={"email"}, message="user.exists")
 */
class User implements AdvancedUserInterface
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=60, unique=true)
     * @Assert\NotBlank()
     * @Assert\Email()
     */
    private $email;

    /**
     * @ORM\Column(type="string", length=64)
     * @Assert\NotBlank()
     * @Assert\Length(min="8")
     */
    private $password;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\LeaveEntitlement", mappedBy="user", cascade={"persist", "remove"})
     */
    private $leaveEntitlement;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\LeaveRequest", mappedBy="user")
     */
    private $leave_requests;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Role", inversedBy="users")
     */
    private $roles;

    public function __construct()
    {
        $this->leave_requests = new ArrayCollection();
        $this->roles = new ArrayCollection();
    }

    /**
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * @param string $password
     */
    public function setPassword($password): void
    {
        $this->password = $password;
    }

    /**
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param string $email
     */
    public function setEmail($email): void
    {
        $this->email = $email;
    }

    /**
     * @return string
     */
    public function getUsername()
    {
        return $this->getEmail();
    }

    public function getSalt()
    {
        return null;
    }

    public function eraseCredentials()
    {
    }

    public function getRoles()
    {
	$roles = ['ROLE_USER'];
	foreach($this->roles as $role)
	{
		$roles[] = $role->getName();
	}
        return $roles;
    }

    public function isAccountNonExpired()
    {
        return true;
    }

    public function isAccountNonLocked()
    {
        return true;
    }

    public function isCredentialsNonExpired()
    {
        return true;
    }

    /**
     * @return bool
     */
    public function isEnabled()
    {
	return true;
    }

    public function getLeaveEntitlement(): ?LeaveEntitlement
    {
	if (!$this->leaveEntitlement) {
            	    $this->leaveEntitlement = new LeaveEntitlement();
            	    $this->leaveEntitlement->setUser($this);
            	}
        return $this->leaveEntitlement;
    }

    public function setLeaveEntitlement(LeaveEntitlement $leaveEntitlement): self
    {
        $this->leaveEntitlement = $leaveEntitlement;

        // set the owning side of the relation if necessary
        if ($this !== $leaveEntitlement->getUser()) {
            $leaveEntitlement->setUser($this);
        }

        return $this;
    }

    /**
     * @return Collection|LeaveRequest[]
     */
    public function getLeaveRequests(): Collection
    {
        return $this->leave_requests;
    }

    public function addLeaveRequest(LeaveRequest $leaveRequest): self
    {
        if (!$this->leave_requests->contains($leaveRequest)) {
            $this->leave_requests[] = $leaveRequest;
            $leaveRequest->setUser($this);
        }

        return $this;
    }

    public function removeLeaveRequest(LeaveRequest $leaveRequest): self
    {
        if ($this->leave_requests->contains($leaveRequest)) {
            $this->leave_requests->removeElement($leaveRequest);
            // set the owning side to null (unless already changed)
            if ($leaveRequest->getUser() === $this) {
                $leaveRequest->setUser(null);
            }
        }

        return $this;
    }

    public function __toString()
    {
	return $this->email;
    }

    public function addRole(Role $role): self
    {
        if (!$this->roles->contains($role)) {
            $this->roles[] = $role;
        }

        return $this;
    }

    public function removeRole(Role $role): self
    {
        if ($this->roles->contains($role)) {
            $this->roles->removeElement($role);
        }

        return $this;
    }
}
