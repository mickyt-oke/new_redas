@extends('user.directorates._layout')

@section('directorate-tabs', '1')

@section('directorate-sections')

<div class="hrm-tabs-wrap">
    <div class="entry-tabs-wrap" style="margin-bottom:0;">
        <div class="entry-tabs" id="entryTabs">
            @php $tabs = [
                ['personnel-strength','fas fa-users','1. Personnel Strength'],
                ['firearms','fas fa-shield-halved','2. Firearms'],
                ['responsibilities','fas fa-list-check','3. Areas of Responsibility'],
                ['activities','fas fa-tasks','4. Activities Carried Out'],
                ['attachments','fas fa-paperclip','5. Supporting Documents'],
                ['general-report','fas fa-file-alt','6. General Report'],
                ['preview','fas fa-eye','8. Preview'],
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

    {{-- TAB 1: Personnel Strength --}}
    <div class="tab-panel active" id="tab-personnel-strength">
        <div class="redas-card" style="margin-bottom:14px;">
            <div class="card-head">
                <div class="card-head-title">
                    <div class="card-head-icon" style="background:#eff6ff;color:#1d4ed8;"><i class="fas fa-sitemap"></i></div>
                    1a. Units Under Provost / Security
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="nis-table">
                        <thead>
                            <tr>
                                <th style="width:80px;">S/N</th>
                                <th>Sub Unit</th>
                                <th style="width:70px;">Action</th>
                            </tr>
                        </thead>
                        <tbody id="provostUnitsBody">
                            <tr class="data-row">
                                <td>1</td>
                                <td>
                                    <input type="text" class="ni provost-unit-name" name="provost_units[0][name]"
                                        placeholder="Enter Sub Unit Name">
                                </td>
                                <td>
                                    <button type="button" class="btn-icon btn-danger remove-row-btn removeProvostUnit"
                                        title="Remove Sub Unit">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <button type="button" class="add-row-btn" id="addProvostUnit">
                    <i class="fas fa-plus"></i> Add Sub Unit
                </button>
            </div>
        </div>

        <div class="redas-card" style="margin-bottom:14px;">
            <div class="card-head">
                <div class="card-head-title">
                    <div class="card-head-icon" style="background:#eff6ff;color:#1d4ed8;"><i class="fas fa-users"></i></div>
                    1b. Total Staff Strength
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="nis-table">
                        <thead>
                            <tr>
                                <th style="width:80px;">S/N</th>
                                <th>Sub Unit</th>
                                <th style="width:180px;">Strength</th>
                            </tr>
                        </thead>
                        <tbody id="staffStrengthBody">
                            <!-- Populated automatically from the Sub Units table above -->
                        </tbody>
                        <tfoot>
                            <tr class="total-row">
                                <td colspan="2"><strong>TOTAL STAFF STRENGTH</strong></td>
                                <td><input type="number" readonly class="ni" id="staffStrengthGrandTotal" value="0"></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
                <small style="color:var(--gray-500);">This section is automatically generated from the Sub Units entered above.</small>
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

    {{-- TAB 2: Firearms --}}
    <div class="tab-panel" id="tab-firearms">
        <div class="redas-card" style="margin-bottom:14px;">
            <div class="card-head">
                <div class="card-head-title">
                    <div class="card-head-icon" style="background:#eff6ff;color:#1d4ed8;"><i class="fas fa-shield-halved"></i></div>
                    2. Firearms
                </div>
            </div>
            <div class="card-body">
                @php
                $firearmGroups = [
                    ['title' => '2A. Firearms: Provost / Security', 'body' => 'provostFirearmsBody', 'prefix' => 'provost_firearms', 'count' => 'provostFirearmCount', 'ammo' => 'provostAmmoTotal'],
                    ['title' => '2B. Firearms: RRS', 'body' => 'rrsFirearmsBody', 'prefix' => 'rrs_firearms', 'count' => 'rrsFirearmCount', 'ammo' => 'rrsAmmoTotal'],
                    ['title' => '2C. Firearms: JTF', 'body' => 'jtfFirearmsBody', 'prefix' => 'jtf_firearms', 'count' => 'jtfFirearmCount', 'ammo' => 'jtfAmmoTotal'],
                ];
                @endphp
                @foreach($firearmGroups as $group)
                <div class="nis-subsection" @if(!$loop->first) style="margin-top:20px;" @endif>
                    <div class="nis-subsection-title"><i class="fas fa-gun"></i> {{ $group['title'] }}</div>
                    <div class="table-responsive">
                        <table class="nis-table">
                            <thead>
                                <tr>
                                    <th style="width:70px;">S/N</th>
                                    <th>Type</th>
                                    <th style="width:180px;">Ammunition</th>
                                    <th style="width:90px;">Action</th>
                                </tr>
                            </thead>
                            <tbody id="{{ $group['body'] }}">
                                <tr class="data-row">
                                    <td>1</td>
                                    <td><input type="text" class="ni firearm-type" name="{{ $group['prefix'] }}[0][type]"></td>
                                    <td><input type="number" min="0" class="ni firearm-ammo" name="{{ $group['prefix'] }}[0][ammunition]"></td>
                                    <td><button type="button" class="btn-icon btn-danger remove-firearm-row"><i class="fas fa-trash"></i></button></td>
                                </tr>
                            </tbody>
                            <tfoot>
                                <tr class="total-row">
                                    <td colspan="2"><strong>Total Firearm Types</strong></td>
                                    <td><input readonly id="{{ $group['count'] }}" class="ni" value="1"></td>
                                </tr>
                                <tr class="total-row">
                                    <td colspan="2"><strong>Total Ammunition</strong></td>
                                    <td><input readonly id="{{ $group['ammo'] }}" class="ni" value="0"></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                    <button type="button" class="add-row-btn" data-body="{{ $group['body'] }}" data-prefix="{{ $group['prefix'] }}">
                        <i class="fas fa-plus"></i> Add Firearm
                    </button>
                </div>
                @endforeach
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

    {{-- TAB 3: Areas of Responsibility --}}
    <div class="tab-panel" id="tab-responsibilities">
        <div class="redas-card" style="margin-bottom:14px;">
            <div class="card-head">
                <div class="card-head-title">
                    <div class="card-head-icon" style="background:#eff6ff;color:#1d4ed8;"><i class="fas fa-list-check"></i></div>
                    3. Areas of Responsibility
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="nis-table">
                        <thead>
                            <tr>
                                <th style="width:70px;">S/N</th>
                                <th>Area of Responsibility</th>
                                <th style="width:90px;">Action</th>
                            </tr>
                        </thead>
                        <tbody id="responsibilityBody">
                            <tr class="data-row">
                                <td>1</td>
                                <td><input type="text" class="ni" name="responsibilities[0][description]" placeholder="Enter Area of Responsibility"></td>
                                <td><button type="button" class="btn-icon btn-danger remove-simple-row"><i class="fas fa-trash"></i></button></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <button type="button" class="add-row-btn" data-body="responsibilityBody" data-prefix="responsibilities">
                    <i class="fas fa-plus"></i> Add Area
                </button>
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

    {{-- TAB 4: Activities Carried Out --}}
    <div class="tab-panel" id="tab-activities">
        <div class="redas-card" style="margin-bottom:14px;">
            <div class="card-head">
                <div class="card-head-title">
                    <div class="card-head-icon" style="background:#eff6ff;color:#1d4ed8;"><i class="fas fa-tasks"></i></div>
                    4. Activities Carried Out
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="nis-table">
                        <thead>
                            <tr>
                                <th style="width:70px;">S/N</th>
                                <th>Activity</th>
                                <th style="width:90px;">Action</th>
                            </tr>
                        </thead>
                        <tbody id="activitiesBody">
                            <tr class="data-row">
                                <td>1</td>
                                <td><input type="text" class="ni" name="activities[0][description]" placeholder="Enter Activity"></td>
                                <td><button type="button" class="btn-icon btn-danger remove-simple-row"><i class="fas fa-trash"></i></button></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <button type="button" class="add-row-btn" data-body="activitiesBody" data-prefix="activities">
                    <i class="fas fa-plus"></i> Add Activity
                </button>
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

    {{-- TAB 5: Supporting Documents --}}
    <div class="tab-panel" id="tab-attachments">
        <div class="redas-card" style="margin-bottom:14px;">
            <div class="card-head">
                <div class="card-head-title">
                    <div class="card-head-icon" style="background:#fef9c3;color:#a16207;"><i class="fas fa-paperclip"></i></div>
                    5. Supporting Documents
                </div>
            </div>
            <div class="card-body">
                <div class="fg">
                    <label><i class="fas fa-paperclip"></i> Attach Supporting Documents</label>
                    <div class="attach-zone" id="attachZone">
                        <i class="fas fa-cloud-upload-alt"></i>
                        <div style="font-size:.85rem;margin-bottom:4px;">Drag &amp; Drop files here</div>
                        <div style="font-size:.75rem;color:var(--gray-500);">Nominal Roll &bull; Attendance Register &bull; Pictures &bull; Circulars &bull; Other Evidence</div>
                        <input type="file" id="attachInput" name="attachments[]" multiple accept=".pdf,.doc,.docx,.jpg,.jpeg,.png" style="display:none;">
                    </div>
                    <div id="attachList" class="attach-list"></div>
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

    {{-- TAB 6: General Report --}}
    <div class="tab-panel" id="tab-general-report">
        <div class="redas-card" style="margin-bottom:14px;">
            <div class="card-head">
                <div class="card-head-title">
                    <div class="card-head-icon" style="background:#eff6ff;color:#1d4ed8;"><i class="fas fa-file-alt"></i></div>
                    6. General Report
                </div>
            </div>
            <div class="card-body">
                <div class="fg">
                    <label>Introduction</label>
                    <textarea name="introduction" class="ni" rows="6" placeholder="Provide a brief overview of the activities of the Provost/Security Unit during the reporting period...">{{ old('introduction') }}</textarea>
                    <small style="color:var(--gray-500);">Summarize the major operations, security situation, achievements and any significant observations during the reporting period.</small>
                </div>

                <div class="nis-subsection" style="margin-top:20px;">
                    <div class="nis-subsection-title"><i class="fas fa-triangle-exclamation"></i> Challenges</div>
                    <div class="table-responsive">
                        <table class="nis-table">
                            <thead>
                                <tr>
                                    <th style="width:70px;">S/N</th>
                                    <th>Challenge</th>
                                    <th style="width:90px;">Action</th>
                                </tr>
                            </thead>
                            <tbody id="challengesBody">
                                <tr class="data-row">
                                    <td>1</td>
                                    <td><input type="text" class="ni" name="challenges[0][description]" placeholder="Enter Challenge"></td>
                                    <td><button type="button" class="btn-icon btn-danger remove-simple-row"><i class="fas fa-trash"></i></button></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <button type="button" class="add-row-btn" data-body="challengesBody" data-prefix="challenges">
                        <i class="fas fa-plus"></i> Add Challenge
                    </button>
                </div>

                <div class="fg" style="margin-top:20px;">
                    <label>Conclusion</label>
                    <textarea name="conclusion" class="ni" rows="6" placeholder="Provide concluding remarks...">{{ old('conclusion') }}</textarea>
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

    {{-- TAB 8: Preview --}}
    <div class="tab-panel" id="tab-preview">
        <div class="redas-card" style="margin-bottom:14px;">
            <div class="card-head">
                <div class="card-head-title">
                    <div class="card-head-icon" style="background:#eff6ff;color:#1d4ed8;"><i class="fas fa-eye"></i></div>
                    8. Review and Submit
                </div>
            </div>
            <div class="card-body">
                <div class="preview-notice" style="background:#fffbeb; border:1px solid #fef3c7; padding:12px; border-radius:6px; color:#92400e; margin-bottom:16px; font-size:.9rem;">
                    <i class="fas fa-exclamation-triangle"></i> Please review all information carefully. Once submitted, the return will move to the Unit Head for approval.
                </div>
                <div id="formPreviewContent">
                    <p style="text-align:center; color:var(--gray-500); padding:20px;">Please use the Previous button to review each section before submitting.</p>
                </div>
            </div>
        </div>
        <div class="hrm-actions">
            <button type="button" class="btn-nis btn-ghost hrm-prev-btn"><i class="fas fa-arrow-left"></i> Previous</button>
            <div class="hrm-actions-center">
                <button type="button" class="btn-nis btn-outline-nis hrm-save-draft-btn"><i class="fas fa-save"></i> Save Draft</button>
            </div>
            <button type="submit" class="btn-nis btn-primary-nis"><i class="fas fa-paper-plane"></i> Submit Provost/Security Return</button>
        </div>
    </div>

</div>

@endsection
