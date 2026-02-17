@extends('layouts.app')

@section('content')
<div class="pageheader">
    <div class="pagetitle">
        <h1><i class="fa fa-file-text"></i> My Ticket Templates</h1>
    </div>
    <div class="header-actions">
        <a href="{{ BASE_URL }}/tickets/customTemplates/create" class="btn btn-primary">
            <i class="fa fa-plus"></i> New Template
        </a>
    </div>
</div>

<div class="maincontent">
    <div class="maincontentinner">
        
        @if(empty($templates))
            <div class="center padding-xl">
                <i class="fa fa-file-text fa-4x" style="color: #ccc; margin-bottom: 20px;"></i>
                <h3>No templates yet</h3>
                <p>Save your frequently used ticket descriptions as templates for quick reuse.</p>
                <a href="{{ BASE_URL }}/tickets/customTemplates/create" class="btn btn-primary">
                    <i class="fa fa-plus"></i> Create Your First Template
                </a>
            </div>
        @else
            <div class="row">
                @foreach($templates as $template)
                    <div class="col-md-4 col-sm-6">
                        <div class="ticketBox" style="margin-bottom: 20px; border: 1px solid #ddd; border-radius: 4px; padding: 15px;">
                            <h4 style="margin-top: 0;">
                                <i class="fa fa-file-text"></i> {{ $template['title'] }}
                            </h4>
                            <div style="color: #666; font-size: 12px; margin-bottom: 10px;">
                                <i class="fa fa-clock-o"></i> {{ date('M d, Y', strtotime($template['created'])) }}
                            </div>
                            <div style="max-height: 100px; overflow: hidden; margin-bottom: 10px; color: #666;">
                                {!! substr(strip_tags($template['content']), 0, 150) !!}...
                            </div>
                            <div style="border-top: 1px solid #eee; padding-top: 10px; margin-top: 10px;">
                                <button class="btn btn-sm btn-primary use-template" 
                                        data-title="{{ $template['title'] }}"
                                        data-content="{{ htmlspecialchars($template['content']) }}">
                                    <i class="fa fa-copy"></i> Use Template
                                </button>
                                <a href="{{ BASE_URL }}/tickets/customTemplates/delete/{{ $template['id'] }}" 
                                   class="btn btn-sm btn-danger"
                                   onclick="return confirm('Delete this template?')">
                                    <i class="fa fa-trash"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
        
    </div>
</div>

<!-- Template Preview Modal -->
<div id="templateModal" class="modal fade" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title" id="templateModalTitle">Template Preview</h4>
            </div>
            <div class="modal-body" id="templateModalContent" style="max-height: 500px; overflow-y: auto;">
                <!-- Content will be inserted here -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="copyTemplateBtn">
                    <i class="fa fa-copy"></i> Copy to Clipboard
                </button>
            </div>
        </div>
    </div>
</div>

<script>
jQuery(document).ready(function() {
    // Show template in modal
    jQuery('.use-template').on('click', function() {
        var title = jQuery(this).data('title');
        var content = jQuery(this).data('content');
        
        jQuery('#templateModalTitle').text(title);
        jQuery('#templateModalContent').html(content);
        jQuery('#templateModal').modal('show');
    });
    
    // Copy template to clipboard
    jQuery('#copyTemplateBtn').on('click', function() {
        var content = jQuery('#templateModalContent').html();
        
        // Create temporary element to copy
        var temp = jQuery('<textarea>');
        jQuery('body').append(temp);
        temp.val(content).select();
        document.execCommand('copy');
        temp.remove();
        
        jQuery('#templateModal').modal('hide');
        
        // Show success notification
        if (typeof leantime !== 'undefined' && leantime.generalController) {
            leantime.generalController.setNotification('Template copied to clipboard!', 'success');
        } else {
            alert('Template copied to clipboard!');
        }
    });
});
</script>
@endsection