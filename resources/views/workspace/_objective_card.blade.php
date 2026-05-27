<div class="card" style="padding: 1rem; background: white; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.05); border: 1px solid #edf2f7; transition: transform 0.2s, box-shadow 0.2s;" onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 4px 6px rgba(0,0,0,0.1)';" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 2px 4px rgba(0,0,0,0.05)';">
    <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 0.5rem;">
        <h4 style="margin: 0; font-size: 1rem; color: #2d3748; font-weight: 700;">{{ $obj->title }}</h4>
        @php
            $periodColors = [
                'daily' => ['bg' => '#e6fffa', 'color' => '#234e52', 'label' => 'Diario'],
                'weekly' => ['bg' => '#ebf8ff', 'color' => '#2b6cb0', 'label' => 'Semanal'],
                'monthly' => ['bg' => '#faf5ff', 'color' => '#553c9a', 'label' => 'Mensual']
            ];
            $p = $periodColors[$obj->period] ?? ['bg' => '#edf2f7', 'color' => '#4a5568', 'label' => ucfirst($obj->period)];
        @endphp
        <span style="background: {{ $p['bg'] }}; color: {{ $p['color'] }}; font-size: 0.7rem; padding: 0.15rem 0.5rem; border-radius: 4px; font-weight: 600;">{{ $p['label'] }}</span>
    </div>
    
    <p style="margin: 0 0 1rem 0; font-size: 0.85rem; color: #718096; line-height: 1.4;">{{ $obj->description }}</p>
    
    @if($obj->due_date)
        <div style="font-size: 0.8rem; color: #a0aec0; margin-bottom: 1rem; display: flex; align-items: center; gap: 0.3rem;">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
            Vence: {{ \Carbon\Carbon::parse($obj->due_date)->format('d/m/Y') }}
        </div>
    @endif

    @if($obj->admin_comment)
        <div style="background: #fffaf0; border-left: 3px solid #ed8936; padding: 0.5rem; margin-bottom: 1rem; font-size: 0.8rem; color: #7b341e;">
            <strong style="display: block; margin-bottom: 0.2rem;">Comentario Admin:</strong>
            {{ $obj->admin_comment }}
        </div>
    @endif

    <div style="display: flex; gap: 0.5rem; justify-content: flex-end; border-top: 1px solid #edf2f7; padding-top: 0.8rem;">
        <button onclick="openNotesModal({{ $obj->id }}, '{{ addslashes($obj->employee_notes) }}')" style="background: none; border: none; color: #4a5568; cursor: pointer; display: flex; align-items: center; gap: 0.2rem; font-size: 0.8rem; font-weight: 600; padding: 0.4rem 0.6rem; border-radius: 4px; transition: background 0.2s;" onmouseover="this.style.background='#edf2f7'" onmouseout="this.style.background='none'">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg>
            Notas
        </button>

        @if($obj->status !== 'completed')
            <form action="{{ route('objectives.update_status', $obj) }}" method="POST" style="margin: 0;">
                @csrf
                <input type="hidden" name="status" value="{{ $obj->status === 'pending' ? 'in_progress' : 'completed' }}">
                <button type="submit" style="background: {{ $obj->status === 'pending' ? '#ebf8ff' : '#f0fff4' }}; border: 1px solid {{ $obj->status === 'pending' ? '#bee3f8' : '#c6f6d5' }}; color: {{ $obj->status === 'pending' ? '#2b6cb0' : '#22543d' }}; cursor: pointer; display: flex; align-items: center; gap: 0.2rem; font-size: 0.8rem; font-weight: 600; padding: 0.4rem 0.6rem; border-radius: 4px;">
                    {{ $obj->status === 'pending' ? 'Iniciar' : 'Completar' }}
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"></polyline></svg>
                </button>
            </form>
        @endif
    </div>
</div>

<!-- Modal para Notas de Empleado (Unique per objective) -->
<div id="notes-modal-{{ $obj->id }}" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 10000; justify-content: center; align-items: center; backdrop-filter: blur(4px);">
    <div class="card" style="width: 480px; max-width: 95%; background: white; border-radius: 12px; padding: 2rem;">
        <h3 style="margin-bottom: 1.5rem; color: var(--primary-color); font-weight: 700;">Notas / Avances del Objetivo</h3>
        
        <form action="{{ route('objectives.update_notes', $obj) }}" method="POST">
            @csrf
            <div style="margin-bottom: 1.5rem;">
                <label style="display: block; margin-bottom: 0.5rem; font-weight: 600;">Mis Notas</label>
                <textarea name="employee_notes" rows="5" placeholder="Registrá acá los avances o notas sobre este objetivo." style="width: 100%; padding: 0.75rem; border-radius: 8px; border: 1px solid #e2e8f0;">{{ $obj->employee_notes }}</textarea>
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 1rem;">
                <button type="button" onclick="closeNotesModal({{ $obj->id }})" class="btn" style="background: #edf2f7; color: var(--text-main);">Cancelar</button>
                <button type="submit" class="btn btn-primary">Guardar Notas</button>
            </div>
        </form>
    </div>
</div>

<script>
    function openNotesModal(id) { document.getElementById('notes-modal-' + id).style.display = 'flex'; }
    function closeNotesModal(id) { document.getElementById('notes-modal-' + id).style.display = 'none'; }
</script>
