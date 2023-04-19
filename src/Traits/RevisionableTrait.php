<?php

namespace Crankd\RapidRevisions\Traits;

use App\Models\Tenant;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Builder;
use Crankd\RapidRevisions\Models\Revision;
use Illuminate\Database\Eloquent\Casts\Attribute;

trait RevisionableTrait
{
    // boot function  only get where model id not in revisions model_id
    protected static function bootRevisionable()
    {
        static::addGlobalScope('revisionable', function ($builder) {
            $builder->whereNotIn('id', Revision::where('revisionables_type', self::class)->pluck('model_id'));
        });
    }

    public function revisions()
    {
        return $this->morphMany(Revision::class, 'revisionables')->orderBy('created_at', 'DESC');
    }


    private function createRevisionForModel($model)
    {
        $data_revision = [
            'created_by' => Auth::check() ? Auth::user()->id : null,
            'model_id' => $model->id,
            'revisionables_id' => $this->id,
            'revisionables_type' => get_class($model),
        ];
        return Revision::create($data_revision);
    }

    public function createRevision($force = false)
    {
        $revisionableLimit = $this->revisionableLimit ?? 10; // Max number of revisions
        $revisionableDelay = $this->revisionableDelay ?? 1; // Hours delay between revisions

        // check if the delay is not met
        if ($force != true && $this->revisions()->where('created_at', '>=', now()->subHours($revisionableDelay))->count() > 0) {
            return false;
        }

        $revision = $this->replicate();
        $revision->saveQuietly();

        // if this has groups
        if ($this->groups->count() > 0) {
            foreach ($this->groups as $group) {
                $pivot = $group->pivot; // Retrieve the pivot entry for the group
                $clonedPivot = $pivot->replicate(); // Create a copy of the pivot entry
                $revision->groups()->attach([$group->id => [
                    'custom_field_values' => $clonedPivot->toArray()['custom_field_values']
                ]]);
            }
            $revision->saveQuietly();
        }

        $this->createRevisionForModel($revision);


        if ($force != true && $this->revisions()->count() >= $revisionableLimit) {
            $this->revisions()->orderBy('created_at', 'ASC')->first()->delete();
        }

        return $revision;
    }


    // restoreRevision
    public static function restoreRevision($revisionable)
    {
        // Current model to be restored dont use the scope revisionable
        $modelCurrent = $revisionable->revisionables_type::withoutGlobalScope('revisionable')->where('id', $revisionable->revisionables_id)->first();
        $modelRestore = $revisionable->revisionables_type::withoutGlobalScope('revisionable')->where('id', $revisionable->model_id)->first();
        // if the modelCurrent has groups
        // get the current model groups
        if ($modelCurrent->groups->count() > 0) {
            // update the group custom_field_values with the revision group custom_field_values
            foreach ($modelCurrent->groups as $group) {
                $values = $modelRestore->groups->where('id', $group->id)->first()->pivot->custom_field_values;
                $modelCurrent->updateGroupValues($group->key, $values);
            }
            // $modelRestore->saveQuietly();
        }
        $modelCurrent->createRevision(true);
        $response = $modelCurrent->update($modelRestore->toArray());

        return $response;
    }

    // previewRevision
    public function previewRevision($revisionable)
    {
        $model = $revisionable->revisionables_type::withoutGlobalScope('revisionable')->where('id', $revisionable->model_id)->first();

        // redirect to show route with the model banks.show
        return redirect()->route('banks.show', $model);
    }
}
