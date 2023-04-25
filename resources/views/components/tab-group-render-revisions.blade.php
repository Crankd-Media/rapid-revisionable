@props([
    'model' => null,
    'previewRoute' => '',
])

<x-rapid::ui.tab-group.panel name="revisions">
	<sl-card class="card-header w-full">
		<div slot="header"
			class="py-3">
			<h3 class="text-lg font-medium leading-6 text-gray-900">Revisions</h3>
		</div>
		<div class="">
			<dl class="sm:divide-y sm:divide-gray-200">
				@foreach ($model->revisions as $revisionable)
					<?php
					$modelRevision = $revisionable->revisionables_type
					    ::withoutGlobalScope('revisionable')
					    ->where('id', $revisionable->model_id)
					    ->first();
					
					$previewUrl = route($previewRoute, $modelRevision);
					
					$model = $revisionable->revisionables_type
					    ::withoutGlobalScope('revisionable')
					    ->where('id', $revisionable->model_id)
					    ->first();
					
					?>
					<div class="py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:py-5">
						<dt class="text-sm font-medium text-gray-500">
							<strong>{{ $revisionable->owner->name }}</strong>
							<p>{{ $revisionable->created_at->diffForHumans() }}</p>
						</dt>
						<dd class="mt-1 text-sm text-gray-900 sm:col-span-1 sm:mt-0">
							<div class="mb-2">{{ $modelRevision->name }}</div>
							@if ($modelRevision->groups->count() > 0)
								<div class="space-y-2">
									@foreach ($modelRevision->groups as $group)
										<x-rapid::ui.accordion :title="$group->name">
											<x-slot name="trigger">
												<div class="bg-black/10 p-1">
													{{ $group->name }}
												</div>
											</x-slot>

											<dl class="sm:divide-y sm:divide-gray-200">
												@foreach ($group->pivot->custom_field_values as $key => $value)
													<div class="py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:py-5">
														<dt class="text-sm font-medium text-gray-500">
															{{ $key }}
														</dt>
														<dd class="mt-1 text-sm text-gray-900 sm:col-span-1 sm:mt-0">
															{{ $value->value }}
														</dd>
													</div>
												@endforeach
											</dl>
										</x-rapid::ui.accordion>
									@endforeach
								</div>
							@endif
						</dd>
						<dd class="mt-1 text-right text-sm text-gray-900 sm:col-span-1 sm:mt-0">

							<x-rapid::ui.button variant="primary-outline"
								href="{{ $previewUrl }}"
								target="_blank">
								Preview
							</x-rapid::ui.button>

							<form class="inline-block"
								action="{{ route('banks.restore', $revisionable->id) }}"
								method="POST"
								data-confirm="true">
								@csrf

								<x-rapid::ui.button variant="warning"
									type="submit">
									Restore
								</x-rapid::ui.button>
							</form>
						</dd>
					</div>
				@endforeach
			</dl>
		</div>
	</sl-card>
</x-rapid::ui.tab-group.panel>
