@extends('layouts.layout-app-admin')
@section('content')
    <testerpage inline-template>
        <div>
            <vue-file-agent :uploadUrl="uploadUrl" v-model="fileRecords"></vue-file-agent>
        </div>
    </testerpage>
@endsection
