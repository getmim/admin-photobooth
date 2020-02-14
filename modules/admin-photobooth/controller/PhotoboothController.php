<?php
/**
 * PhotoboothController
 * @package admin-photobooth
 * @version 0.0.1
 */

namespace AdminPhotobooth\Controller;

use Event\Model\Event;
use LibFormatter\Library\Formatter;
use LibForm\Library\Form;
use LibPagination\Library\Paginator;
use Photobooth\Model\Photobooth as Photobooth;

class PhotoboothController extends \Admin\Controller
{
    private function getParams(string $title): array{
        return [
            '_meta' => [
                'title' => $title,
                'menus' => ['photobooth']
            ],
            'subtitle' => $title,
            'pages' => null
        ];
    }

    public function editAction(){
        if(!$this->user->isLogin())
            return $this->loginFirst(1);
        if(!$this->can_i->manage_photobooth)
            return $this->show404();

        $pbooth = (object)[];

        $id = $this->req->param->id;
        if($id){
            $pbooth = Photobooth::getOne(['id'=>$id]);
            if(!$pbooth)
                return $this->show404();
            $params = $this->getParams('Edit Photobooth');
        }else{
            $params = $this->getParams('Create New Photobooth');
        }

        $form              = new Form('admin.photobooth.edit');
        $params['form']    = $form;

        if(!($valid = $form->validate($pbooth)) || !$form->csrfTest('noob'))
            return $this->resp('photobooth/edit', $params);

        if($id){
            if(!Photobooth::set((array)$valid, ['id'=>$id]))
                deb(Photobooth::lastError());
        }else{
            $valid->user = $this->user->id;
            if(!Photobooth::create((array)$valid))
                deb(Photobooth::lastError());
        }

        // add the log
        $this->addLog([
            'user'   => $this->user->id,
            'object' => $id,
            'parent' => 0,
            'method' => $id ? 2 : 1,
            'type'   => 'photobooth',
            'original' => $pbooth,
            'changes'  => $valid
        ]);

        $next = $this->router->to('adminPhotobooth');
        $this->res->redirect($next);
    }

    public function indexAction(){
        if(!$this->user->isLogin())
            return $this->loginFirst(1);
        if(!$this->can_i->manage_photobooth)
            return $this->show404();

        $ap_event = module_exists('admin-photobooth-event');

        $cond = $pcond = [];
        if($q = $this->req->getQuery('q'))
            $pcond['q'] = $cond['q'] = $q;
        if($ap_event && $event = $this->req->getQuery('event'))
            $pcond['event'] = $cond['event'] = $event;

        list($page, $rpp) = $this->req->getPager(25, 50);

        $pbooths = Photobooth::get($cond, $rpp, $page, ['fullname'=>true]) ?? [];
        if($pbooths){
            $fmt = ['user'];
            if(module_exists('photobooth-event'))
                $fmt[] = 'event';
            $pbooths = Formatter::formatMany('photobooth', $pbooths, $fmt);
        }

        $params             = $this->getParams('Photobooth');
        $params['pbooths']  = $pbooths;
        $params['form']     = new Form('admin.photobooth.index');
        $params['event']    = [];

        if(isset($cond['event'])){
            $event = Event::getOne(['id'=>$cond['event']]);
            if($event)
                $params['event'][$event->id] = $event->title;
        }

        $params['form']->validate( (object)$this->req->get() );

        // pagination
        $params['total'] = $total = Photobooth::count($cond);
        if($total > $rpp){
            $params['pages'] = new Paginator(
                $this->router->to('adminPhotobooth'),
                $total,
                $page,
                $rpp,
                10,
                $pcond
            );
        }

        if(module_exists('admin-photobooth-text'))
            $params['sms_form'] = new Form('admin.photobooth.sms');
        
        $this->resp('photobooth/index', $params);
    }

    public function removeAction(){
        if(!$this->user->isLogin())
            return $this->loginFirst(1);
        if(!$this->can_i->manage_photobooth)
            return $this->show404();

        $id     = $this->req->param->id;
        $pbooth = Photobooth::getOne(['id'=>$id]);
        $next   = $this->router->to('adminPhotobooth');
        $form   = new Form('admin.photobooth.index');

        if(!$form->csrfTest('noob'))
            return $this->res->redirect($next);

        // add the log
        $this->addLog([
            'user'   => $this->user->id,
            'object' => $id,
            'parent' => 0,
            'method' => 3,
            'type'   => 'photobooth',
            'original' => $pbooth,
            'changes'  => null
        ]);

        Photobooth::remove(['id'=>$id]);

        $this->res->redirect($next);
    }
}