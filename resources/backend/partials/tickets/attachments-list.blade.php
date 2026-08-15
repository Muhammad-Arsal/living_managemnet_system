@if ($attachments->isNotEmpty())
    <ul class="lms-ticket-attachments">
        @foreach ($attachments as $attachment)
            <li>
                <a href="{{ route($portal.'.tickets.attachments.download', [$ticket, $attachment]) }}">
                    <i class="iconify" data-icon="bx:bx-paperclip"></i>
                    <span>{{ $attachment->original_name }}</span>
                    <small>{{ $attachment->formattedSize() }}</small>
                </a>
            </li>
        @endforeach
    </ul>
@endif
