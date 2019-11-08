<?php

namespace App\Actions;

use DB;
use Carbon\Carbon;
use App\Models\Event;

class CreateEventAction
{
    protected $colors = ['blue', 'indigo', 'purple', 'cyan', 'deep-orange'];
    protected $data;

    public function __construct()
    {
        // $this->setFormData();
        $this->setFormData()->setParent();
    }

    /**
     * Execute the action and return a result.
     *
     * @return mixed
     */
    public function execute () {
        DB::beginTransaction();
        try {
            $this->createEvent();
            DB::commit();
        } catch (\Exception $ex) {
            DB::rollback(); throw $ex;
        }
        return collect($this->data)->except(['updated_at', 'created_at']);
    }

    protected function setFormData () {
        $this->data          = request()->all();
        $this->data['color'] = request('color') ?? $this->colors[array_rand($this->colors)];
        return $this;
    }

    protected function setParent ()
    {
        if ( empty($this->data['id']) ) {
            $event                   = new Event();
            $event->title            = $this->data['title'];
            $event->parent_id        = $this->data['parent_id'];
            $event->color            = $this->data['color'];
            $event->start_date       = $this->data['start'];
            $event->end_date         = $this->data['end'];
            $isSaved                 = $event->save();
            $this->data['id']        = $isSaved ? $event->id : null;
        }
    }

    public function createEvent ()
    {
        $days   = Carbon::parse($this->data['start'])->daysUntil($this->data['end']);
        $ranges = collect([]);

        if ( !empty(request('id')) ) {
            Event::where('parent_id', $this->data['id'])->delete();
        }

        collect($days)->each(function ($day) use ($ranges) {
            $day = Carbon::parse($day);

            $event             = new \stdClass;
            $event->title      = $this->data['title'];
            $event->parent_id  = $this->data['id'];
            $event->color      = $this->data['color'];

            if ( in_array($day->format('D'), $this->data['days']) || count(request('days')) === 0 ) {
                $day               = $day->format('Y-m-d');
                $event->start_date = $day;
                $event->end_date   = $day;
                $ranges->push(collect($event)->toArray());
            }
        });

        Event::insert($ranges->toArray());

        return $ranges->toArray();
    }
}
