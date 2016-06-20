<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="homepage")
     * @Template(":default:home.html.twig")
     */
    public function homeAction(Request $request)
    {
        return [];
    }

    /**
     * @Route("/profile", name="profile")
     * @Template(":default:profile.html.twig")
     */
    public function profileAction(Request $request)
    {
        return ['message' => 'Your are authenticated.'];
    }

    /**
     * @Route("/profile/two-factor", name="profile_two_factor")
     * @Template(":default:profile.html.twig")
     */
    public function profileTwoFactorAction(Request $request)
    {
        return ['message' => 'Your are authenticated with two factor.'];
    }
}
