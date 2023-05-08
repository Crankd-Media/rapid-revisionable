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

		<x-rapid-revisions::details :model="$model"
			:previewRoute="$previewRoute" />
	</sl-card>

</x-rapid::ui.tab-group.panel>
