@extends('layouts.header')

@section('content')
    <div class="container py-4">
        <div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
            <div>
                <h1 class="h3 mb-1">Chatbot NLP training</h1>
                <p class="mb-0">Add labeled examples and retrain the intent and entity model.</p>
            </div>
            <span class="badge badge-primary">Examples added: {{ $exampleCount }}</span>
        </div>

        @if(session('status'))
            <div class="alert alert-success" role="status">{{ session('status') }}</div>
        @endif

        @if($errors->any())
            <div class="alert alert-danger" role="alert">
                <ul class="mb-0">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="card mb-4">
            <div class="card-header">Add a training example</div>
            <div class="card-body">
                <form method="POST" action="{{ route('chatbot.training.examples.store') }}">
                    @csrf
                    <div class="form-group">
                        <label for="text">Example message</label>
                        <input id="text" name="text" class="form-control" maxlength="500" required
                               value="{{ old('text') }}" placeholder="e.g. Could you tell me how many visitors came this week?">
                    </div>
                    <div class="form-group">
                        <label for="intent">Intent</label>
                        <select id="intent" name="intent" class="form-control" required>
                            @foreach($intents as $intent)
                                <option value="{{ $intent }}" @selected(old('intent') === $intent)>{{ str_replace('_', ' ', ucfirst($intent)) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-4">
                            <label for="gender_entity">Gender entity (optional)</label>
                            <input id="gender_entity" name="gender_entity" class="form-control" maxlength="40"
                                   value="{{ old('gender_entity') }}" placeholder="e.g. female">
                        </div>
                        <div class="form-group col-md-4">
                            <label for="period_entity">Period entity (optional)</label>
                            <input id="period_entity" name="period_entity" class="form-control" maxlength="80"
                                   value="{{ old('period_entity') }}" placeholder="e.g. last week">
                        </div>
                        <div class="form-group col-md-4">
                            <label for="month_entity">Month entity (optional)</label>
                            <input id="month_entity" name="month_entity" class="form-control" maxlength="30"
                                   value="{{ old('month_entity') }}" placeholder="e.g. June">
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary">Save example</button>
                </form>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-header">Retrain model</div>
            <div class="card-body">
                <p>The model will be rebuilt using the repository examples and all examples added here.</p>
                <form method="POST" action="{{ route('chatbot.training.train') }}">
                    @csrf
                    <button type="submit" class="btn btn-success">Train chatbot model</button>
                </form>
            </div>
        </div>

        <div class="card">
            <div class="card-header">Recently added examples</div>
            <div class="table-responsive">
                <table class="table table-striped mb-0">
                    <thead>
                    <tr>
                        <th scope="col">Message</th>
                        <th scope="col">Intent</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($recentExamples as $example)
                        <tr>
                            <td>{{ $example['text'] }}</td>
                            <td>{{ str_replace('_', ' ', $example['intent']) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="2" class="text-center">No examples have been added through this page yet.</td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
