<?php

namespace Kurumi\MainBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Briareos\AjaxBundle\Ajax;
use JMS\DiExtraBundle\Annotation as DI;

class InfoController extends Controller
{
    /**
     * @DI\Inject("router")
     *
     * @var \Symfony\Bundle\FrameworkBundle\Routing\Router
     */
    private $router;

    /**
     * @DI\Inject("doctrine.orm.default_entity_manager")
     *
     * @var \Doctrine\ORM\EntityManager
     */
    private $em;

    /**
     * @DI\Inject("templating.ajax")
     *
     * @var \Briareos\AjaxBundle\Twig\AjaxEngine
     */
    private $ajax;

    /**
     * @DI\Inject("templating.ajax.helper")
     *
     * @var \Briareos\AjaxBundle\Ajax\Helper
     */
    private $ajaxHelper;

    /**
     * @Route("/info/faq", name="info_faq")
     */
    public function faqAction()
    {
        $faqs = [
            [
                'question' => "What are Super Powers?",
                'answer' => "Super Powers help you to meet people faster. By subscribing to Super Powers you can take full advantage of all the extra features on offer including Profile Customization, Invisible Mode, Advanced search and many more.To enjoy the Super Powers experience just click on the icon 'Activate Super Powers' shown on your Kurumi profile and follow the instructions. To manage your subscription please see 'Payment Settings'.",
            ],
            [
                'question' => "How can I control my privacy?",
                'answer' => "You can manage your privacy through the 'Settings' link in the corner of every page. Options are available to control who can view your profile and contact you, how to adjust your sign in security level, options to protect your photos by switching on watermarks and many more.<br><br>For more details about your Privacy, please refer to our Privacy Policy.",
            ],
            [
                'question' => "How do Credits work?",
                'answer' => "You can get much more attention on Kurumi by purchasing Credits. These allow you to send gifts, get displayed more times in the Encounters game, Rise up in the search results and be shown in the Spotlight at the top of almost every page on Kurumi, encouraging more people to visit your profile. You can also enable an automatic top-up feature to make sure you never run out of credits!<br><br>You can manage your automatic top-up in Payment Settings.",
            ],
            [
                'question' => "How can I manage my online safety?",
                'answer' => "Please see Kurumi Safety Tips, which can be found at the bottom of each page. We strongly advise you never to give out personal or financial information over the Internet and can confirm that Kurumi will never ask you for this information via the 'Messages' section.<br><br>If you have further questions about safety on the site please contact our Customer Support Team.",
            ],
            [
                'question' => "I've forgotten my password. What do I do?",
                'answer' => "Just click on the link Forgot Password? on the Sign In page and follow the simple instructions to reset it.",
            ],
            [
                'question' => "How do I delete my profile?",
                'answer' => "If you really want to delete your Kurumi profile, just log into your account and click on 'Settings'. Then select the 'Delete Profile' link from the left hand side of the page and follow the instructions given. If you want to know more, please see our Privacy Policy.",
            ],
            [
                'question' => "What if I have any payment issues?",
                'answer' => "If you have any enquiries relating to payment issues, please don’t hesitate to contact our Customer Support Team who are always happy to help.",
            ],
        ];

        $templateFile = ':Info:faq.html.twig';
        $templateParams = [
            'user' => $this->getUser(),
            'faqs' => $faqs,
        ];
        $url = $this->router->generate('info_faq');

        if ($this->getRequest()->isXmlHttpRequest()) {
            return $this->ajaxHelper->renderPjaxBlock($templateFile, $templateParams, $url, 'info_page');
        } else {
            return $this->render($templateFile, $templateParams);
        }
    }

    /**
     * @Route("/info/general", name="info_general")
     */
    public function generalAction()
    {
        $generals = [
            ''
        ];
    }
}
