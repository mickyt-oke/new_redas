@extends('user.directorates._layout')

@section('directorate-tabs', '1')

@section('directorate-sections')

<div class="hrm-tabs-wrap">
    <div class="entry-tabs-wrap" style="margin-bottom:0;">
        <div class="entry-tabs" id="entryTabs">
            @php $tabs = [
                ['performance-appraisals','fas fa-clipboard-check','1. Performance Appraisals'],
                ['performance-targets','fas fa-bullseye','2. Performance Targets'],
                ['attachments','fas fa-paperclip','3. Attachments'],
                ['general-report','fas fa-file-alt','4. General Report'],
                ['preview','fas fa-eye','5. Preview'],
            ]; @endphp
            @foreach($tabs as $i => [$id,$icon,$label])
            <button type="button" class="entry-tab {{ $i === 0 ? 'active' : '' }}" data-tab="{{ $id }}">
                <span class="tab-dot"></span>
                <i class="{{ $icon }}" style="font-size:.78rem;"></i>
                {{ $label }}
            </button>
            @endforeach
        </div>
    </div>
</div>

<div class="tab-content">

    {{-- TAB 1: Performance Appraisals --}}
    <div class="tab-panel active" id="tab-performance-appraisals">
        <div class="redas-card" style="margin-bottom:14px;">
            <div class="card-head">
                <div class="card-head-title">
                    <div class="card-head-icon" style="background:#eff6ff;color:#1d4ed8;"><i class="fas fa-clipboard-check"></i></div>
                    1. Performance Appraisals
                </div>
            </div>
            <div class="card-body" style="display:grid;grid-template-columns:repeat(auto-fit,minmax(220px,1fr));gap:12px;">
                <div class="fg">
                    <label>Appraisals Due</label>
                    <input type="number" class="ni" name="appraisals_due" min="0" value="{{ old('appraisals_due', $editing->return_data['appraisals_due'] ?? '') }}">
                </div>
                <div class="fg">
                    <label>Appraisals Completed</label>
                    <input type="number" class="ni" name="appraisals_completed" min="0" value="{{ old('appraisals_completed', $editing->return_data['appraisals_completed'] ?? '') }}">
                </div>
                <div class="fg">
                    <label>Appraisals Overdue</label>
                    <input type="number" class="ni" name="appraisals_overdue" min="0" value="{{ old('appraisals_overdue', $editing->return_data['appraisals_overdue'] ?? '') }}">
                </div>
            </div>
        </div>
        <div class="hrm-actions">
            <button type="button" class="btn-nis btn-ghost hrm-prev-btn" disabled><i class="fas fa-arrow-left"></i> Previous</button>
            <div class="hrm-actions-center">
                <button type="button" class="btn-nis btn-outline-nis hrm-save-draft-btn"><i class="fas fa-save"></i> Save Draft</button>
            </div>
            <button type="button" class="btn-nis btn-primary-nis hrm-next-btn">Next <i class="fas fa-arrow-right"></i></button>
        </div>
    </div>

    {{-- TAB 2: Performance Targets --}}
    <div class="tab-panel" id="tab-performance-targets">
        <div class="redas-card" style="margin-bottom:14px;">
            <div class="card-head">
                <div class="card-head-title">
                    <div class="card-head-icon" style="background:#eff6ff;color:#1d4ed8;"><i class="fas fa-bullseye"></i></div>
                    2. Performance Targets
                </div>
            </div>
            <div class="card-body" style="display:grid;grid-template-columns:repeat(auto-fit,minmax(220px,1fr));gap:12px;">
                <div class="fg">
                    <label>Targets Set</label>
                    <input type="number" class="ni" name="targets_set" min="0" value="{{ old('targets_set', $editing->return_data['targets_set'] ?? '') }}">
                </div>
                <div class="fg">
                    <label>Targets Met</label>
                    <input type="number" class="ni" name="targets_met" min="0" value="{{ old('targets_met', $editing->return_data['targets_met'] ?? '') }}">
                </div>
                <div class="fg" style="grid-column:1/-1;">
                    <label>Remarks</label>
                    <textarea class="ni" name="remarks" rows="3">{{ old('remarks', $editing->return_data['remarks'] ?? '') }}</textarea>
                </div>
            </div>
        </div>
        <div class="hrm-actions">
            <button type="button" class="btn-nis btn-ghost hrm-prev-btn"><i class="fas fa-arrow-left"></i> Previous</button>
            <div class="hrm-actions-center">
                <button type="button" class="btn-nis btn-outline-nis hrm-save-draft-btn"><i class="fas fa-save"></i> Save Draft</button>
            </div>
            <button type="button" class="btn-nis btn-primary-nis hrm-next-btn">Next <i class="fas fa-arrow-right"></i></button>
        </div>
    </div>

    {{-- TAB 3: Attachments --}}
    <div class="tab-panel" id="tab-attachments">
        <div class="redas-card" style="margin-bottom:14px;">
            <div class="card-head">
                <div class="card-head-title">
                    <div class="card-head-icon" style="background:#eff6ff;color:#1d4ed8;"><i class="fas fa-paperclip"></i></div>
                    3. Supporting Documents & Attachments
                </div>
            </div>
            <div class="card-body">
                <div class="fg">
                    <label><i class="fas fa-paperclip"></i> Attach Supporting Documents (PDF, XLS, XLSX, PNG, JPG)</label>
                    <input type="file" class="ni" name="supporting_documents[]" accept=".pdf,.xls,.xlsx,.png,.jpg,.jpeg" multiple>
                </div>
                <div class="fg" style="margin-top:16px;">
                    <label><i class="fas fa-paperclip"></i> Other Attachments (PDF, DOC, DOCX, JPG, PNG)</label>
                    <input type="file" class="ni" name="attachments[]" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png" multiple>
                </div>
            </div>
        </div>
        <div class="hrm-actions">
            <button type="button" class="btn-nis btn-ghost hrm-prev-btn"><i class="fas fa-arrow-left"></i> Previous</button>
            <div class="hrm-actions-center">
                <button type="button" class="btn-nis btn-outline-nis hrm-save-draft-btn"><i class="fas fa-save"></i> Save Draft</button>
            </div>
            <button type="button" class="btn-nis btn-primary-nis hrm-next-btn">Next <i class="fas fa-arrow-right"></i></button>
        </div>
    </div>

    {{-- TAB 4: General Report --}}
    <div class="tab-panel" id="tab-general-report">
        <div class="redas-card" style="margin-bottom:14px;">
            <div class="card-head">
                <div class="card-head-title">
                    <div class="card-head-icon" style="background:#eff6ff;color:#1d4ed8;"><i class="fas fa-file-alt"></i></div>
                    4. General Report & Summary
                </div>
            </div>
            <div class="card-body">
                <div class="fg">
                    <label>Executive Summary / General Remarks</label>
                    <textarea class="ni" name="general_remarks" rows="8" placeholder="Provide a detailed summary of EPMS activities and achievements for the period..."></textarea>
                </div>
                <div class="fg" style="margin-top:16px;">
                    <label>Challenges Encountered</label>
                    <textarea class="ni" name="challenges" rows="5" placeholder="List any obstacles faced during the implementation of EPMS measures..."></textarea>
                </div>
                <div class="fg" style="margin-top:16px;">
                    <label>Recommendations / Future Action Plan</label>
                    <textarea class="ni" name="recommendations" rows="5" placeholder="Suggest improvements or outline planned activities for the next period..."></textarea>
                </div>
            </div>
        </div>
        <div class="hrm-actions">
            <button type="button" class="btn-nis btn-ghost hrm-prev-btn"><i class="fas fa-arrow-left"></i> Previous</button>
            <div class="hrm-actions-center">
                <button type="button" class="btn-nis btn-outline-nis hrm-save-draft-btn"><i class="fas fa-save"></i> Save Draft</button>
            </div>
            <button type="button" class="btn-nis btn-primary-nis hrm-next-btn">Next <i class="fas fa-arrow-right"></i></button>
        </div>
    </div>

    {{-- TAB 5: Preview --}}
    <div class="tab-panel" id="tab-preview">
        <div class="redas-card" style="margin-bottom:14px;">
            <div class="card-head">
                <div class="card-head-title">
                    <div class="card-head-icon" style="background:#eff6ff;color:#1d4ed8;"><i class="fas fa-eye"></i></div>
                    5. Preview Submission
                </div>
            </div>
            <div class="card-body">
                <div id="preview-content" class="preview-box">
                    <p class="text-muted">Please review your entries before final submission. Use the previous tabs to make corrections.</p>
                </div>
            </div>
        </div>
        <div class="hrm-actions">
            <button type="button" class="btn-nis btn-ghost hrm-prev-btn"><i class="fas fa-arrow-left"></i> Previous</button>
            <div class="hrm-actions-center">
                <button type="button" class="btn-nis btn-outline-nis hrm-save-draft-btn"><i class="fas fa-save"></i> Save Draft</button>
            </div>
            <button type="submit" class="btn-nis btn-primary-nis">
                <i class="fas fa-paper-plane"></i> Submit Return
            </button>
        </div>
    </div>

</div>

@endsection

@include('partials.footer')
