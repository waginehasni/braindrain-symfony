<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use CMEN\GoogleChartsBundle\GoogleCharts\Charts\BarChart;

class BackController extends AbstractController
{
    /**
     * @Route("/back", name="back")
     */
    public function index(): Response
    {
        return $this->render('back/index.html.twig', [
            'controller_name' => 'BackController',
        ]);
    }
    /**
     * @Route("/stats", name="statsreservation")
     */
    public function stats() : Response
    {

        $p=$this->getDoctrine()->getRepository(Reservation::class);
        //year
        $years = $p->getYear();
        $data = [['Years', 'Nombre de reservations']];
        foreach($years as $year)
        {
            $data[] = array($year['year'], $year['post']);
        }

        $bar1 = new barchart();
        $bar1->getData()->setArrayToDataTable(
            $data
        );
        $bar1->getOptions()->setTitle('par années');
        $bar1->getOptions()->getTitleTextStyle()->setColor('#07600');
        $bar1->getOptions()->getTitleTextStyle()->setFontSize(25);

        //month
        $months = $p->getMonth();
        $data = [['Mois', 'Nombre de reservations']];
        foreach($months as $month)
        {
            $data[] = array($month['month'], $month['post']);
        }

        $bar2 = new barchart();
        $bar2->getData()->setArrayToDataTable(
            $data
        );
        $bar2->getOptions()->setTitle('par mois');
        $bar2->getOptions()->getTitleTextStyle()->setColor('#07600');
        $bar2->getOptions()->getTitleTextStyle()->setFontSize(25);

        //day
        $days = $p->getDay();
        $data = [['Années', 'Nombre de reservations']];
        foreach($days as $day)
        {
            $data[] = array($day['day'], $day['post']);
        }

        $bar3 = new barchart();
        $bar3->getData()->setArrayToDataTable(
            $data
        );
        $bar3->getOptions()->setTitle('par jour');
        $bar3->getOptions()->getTitleTextStyle()->setColor('#07600');
        $bar3->getOptions()->getTitleTextStyle()->setFontSize(25);


        return $this->render('reservation/stats.html.twig', array('barchart1' => $bar1, 'barchart2' => $bar2,'barchart3' => $bar3));
    }

    /**
     *
     * @Route("accueilback")
     */
    public function indexback(): Response
    {
        return $this->render('back/index.html.twig', [

        ]);
    }


}
