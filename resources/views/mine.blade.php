<h1 class="page_title">Mine</h1>
<div id="mine">
    <x-skillActionContainer do-action-text="Mine"
        cancel-action-text="Cancel mining" finish-action-text="Fetch minerals"
        action-type-label="Minerals" :show-permits="true" :action-items="$action_items"
        :permits="$permits" :workforce-data="$workforce_data" />
</div>
