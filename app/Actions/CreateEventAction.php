<?php

namespace App\Actions;

use DB;
use App\Models\Event;

class CreateEventAction
{
    protected $colors = ['blue', 'indigo', 'purple', 'cyan', 'deep-orange'];
    protected $data;

    public function __construct()
    {
        $this->setFormData()->setParent();
    }

    /**
     * Execute the action and return a result.
     *
     * @return mixed
     */
    public function execute () {
        $this->createEvent();
        return collect($this->data)->except(['updated_at', 'created_at']);
    }

    protected function setFormData () {
        $this->data          = request()->all();
        $this->data['color'] = $this->colors[array_rand($this->colors)];
        return $this;
    }

    protected function setParent ()
    {
        DB::beginTransaction();

        try {
            if ( empty($this->data['id']) ) {
                $event                   = new Event();
                $event->title            = $this->data['title'];
                $event->parent_id        = $this->data['parent_id'];
                $event->color            = $this->data['color'];
                $event->start_date       = $this->data['start'];
                $event->end_date         = $this->data['end'];
                $isSaved                 = $event->save();
                $this->data['id']        = $isSaved ? $event->id : null;
                DB::commit();
            }
        } catch (\Exception $ex) {
            DB::rollback(); throw $ex;
        }

    }

    public function createEvent ()
    {
        $dates = collect([]);
    }
}
