<footer class="border-top py-4 mt-5">
    <div class="d-flex flex-column flex-sm-row align-items-center justify-content-between gap-2 small text-secondary">
        <span>&copy; {{ date('Y') }} Philip Education. All rights reserved.</span>
        <div class="d-flex gap-3">
            <a href="{{ route('lessons.index') }}" class="link-secondary text-decoration-none">Lessons</a>
            <a href="{{ route('tests.index') }}" class="link-secondary text-decoration-none">Tests</a>
            <a href="{{ route('profiles.index') }}" class="link-secondary text-decoration-none">Profile</a>
        </div>
    </div>
</footer>
