@extends('layouts.customer')

@section('title', 'Support')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-0">Support &amp; Complaints</h4>
        <a href="{{ route('support.mine') }}" class="btn btn-outline-brand btn-sm">My Messages</a>
    </div>

    <div class="row g-4">
        <div class="col-lg-7">
            <div class="card">
                <div class="card-header"><i class="bi bi-chat-square-dots me-1"></i> Submit a Message</div>
                <div class="card-body">
                    <form action="{{ route('support.store') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">Subject</label>
                            <input type="text" name="subject" class="form-control" value="{{ old('subject') }}" placeholder="e.g. Issue with my order" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Message</label>
                            <textarea name="message" rows="5" class="form-control" placeholder="Describe your issue in detail..." required>{{ old('message') }}</textarea>
                        </div>
                        <button type="submit" class="btn btn-brand">
                            <i class="bi bi-send me-1"></i> Send Message
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-5">
            <div class="card">
                <div class="card-header"><i class="bi bi-info-circle me-1"></i> Need help?</div>
                <div class="card-body small">
                    <ul class="list-unstyled mb-0">
                        <li class="mb-2"><i class="bi bi-telephone text-brand me-2"></i>+1 (555) 123-4567</li>
                        <li class="mb-2"><i class="bi bi-envelope text-brand me-2"></i>support@shopease.test</li>
                        <li class="mb-2"><i class="bi bi-clock text-brand me-2"></i>Mon–Sat, 9:00 AM – 6:00 PM</li>
                        <li><i class="bi bi-life-preserver text-brand me-2"></i>Average response time: 24 hours</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
@endsection
