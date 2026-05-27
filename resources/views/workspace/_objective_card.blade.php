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


    <div style="display: flex; gap: 0.5rem; justify-content: flex-end; border-top: 1px solid #edf2f7; padding-top: 0.8rem;">
        <button onclick="openNotesModal({{ $obj->id }})" style="background: none; border: none; color: #4a5568; cursor: pointer; display: flex; align-items: center; gap: 0.2rem; font-size: 0.8rem; font-weight: 600; padding: 0.4rem 0.6rem; border-radius: 4px; transition: background 0.2s;" onmouseover="this.style.background='#edf2f7'" onmouseout="this.style.background='none'">
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

<!-- Modal de Detalles del Objetivo -->
<div id="notes-modal-{{ $obj->id }}" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 10000; justify-content: center; align-items: center; backdrop-filter: blur(4px);">
    <div class="card" style="width: 700px; max-width: 95%; max-height: 90vh; background: white; border-radius: 12px; padding: 2rem; display: flex; flex-direction: column;">
        <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 1.5rem; border-bottom: 1px solid #edf2f7; padding-bottom: 1rem;">
            <div>
                <h3 style="margin: 0 0 0.5rem 0; color: var(--primary-color); font-weight: 800; font-size: 1.4rem;">{{ $obj->title }}</h3>
                <div style="display: flex; gap: 1rem; font-size: 0.85rem; color: #718096;">
                    <span><strong style="color: #4a5568;">Asignado por:</strong> {{ $obj->creator->name ?? 'Admin' }}</span>
                    @if($obj->due_date)
                        <span><strong style="color: #4a5568;">Vence:</strong> {{ \Carbon\Carbon::parse($obj->due_date)->format('d/m/Y') }}</span>
                    @endif
                </div>
            </div>
            <button type="button" onclick="closeNotesModal({{ $obj->id }})" style="background: none; border: none; cursor: pointer; color: #a0aec0; transition: color 0.2s;" onmouseover="this.style.color='#4a5568'" onmouseout="this.style.color='#a0aec0'">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
            </button>
        </div>

        <div style="flex: 1; overflow-y: auto; padding-right: 0.5rem; margin-bottom: 1.5rem;">
            <div style="margin-bottom: 1.5rem;">
                <h4 style="font-size: 0.95rem; color: #4a5568; margin: 0 0 0.5rem 0;">Descripción del Objetivo</h4>
                <p style="margin: 0; font-size: 0.95rem; color: #2d3748; line-height: 1.5; background: #f8fafc; padding: 1rem; border-radius: 8px; border: 1px solid #e2e8f0;">{{ $obj->description }}</p>
            </div>

            <h4 style="font-size: 0.95rem; color: #4a5568; margin: 0 0 1rem 0; border-bottom: 2px solid #edf2f7; padding-bottom: 0.5rem;">Historial y Comentarios</h4>
            
            <div style="display: flex; flex-direction: column; gap: 1rem;">
                @forelse($obj->comments as $comment)
                    <div style="background: {{ $comment->user_id === \Illuminate\Support\Facades\Auth::id() ? '#ebf8ff' : '#f7fafc' }}; padding: 1rem; border-radius: 8px; border: 1px solid {{ $comment->user_id === \Illuminate\Support\Facades\Auth::id() ? '#bee3f8' : '#e2e8f0' }};">
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.5rem;">
                            <strong style="font-size: 0.85rem; color: {{ $comment->user_id === \Illuminate\Support\Facades\Auth::id() ? '#2b6cb0' : '#4a5568' }};">{{ $comment->user->name ?? 'Usuario' }}</strong>
                            <span style="font-size: 0.75rem; color: #a0aec0;">{{ $comment->created_at->format('d/m/Y H:i') }}</span>
                        </div>
                        <div style="font-size: 0.95rem; color: #2d3748; white-space: pre-wrap; margin-bottom: {{ $comment->file_path ? '0.8rem' : '0' }};">{{ $comment->comment }}</div>
                        
                        @if($comment->file_path)
                            <a href="{{ Storage::url($comment->file_path) }}" target="_blank" style="display: inline-flex; align-items: center; gap: 0.4rem; font-size: 0.8rem; background: white; padding: 0.4rem 0.8rem; border-radius: 6px; border: 1px solid #cbd5e0; color: #4a5568; text-decoration: none; font-weight: 600; transition: all 0.2s;" onmouseover="this.style.borderColor='#a0aec0'; this.style.boxShadow='0 2px 4px rgba(0,0,0,0.05)'" onmouseout="this.style.borderColor='#cbd5e0'; this.style.boxShadow='none'">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21.44 11.05l-9.19 9.19a6 6 0 0 1-8.49-8.49l9.19-9.19a4 4 0 0 1 5.66 5.66l-9.2 9.19a2 2 0 0 1-2.83-2.83l8.49-8.48"></path></svg>
                                {{ $comment->file_name ?? 'Ver archivo adjunto' }}
                            </a>
                        @endif
                    </div>
                @empty
                    <div style="text-align: center; padding: 2rem; color: #a0aec0; font-size: 0.9rem; background: #f8fafc; border-radius: 8px; border: 1px dashed #cbd5e0;">
                        No hay comentarios o avances registrados aún.
                    </div>
                @endforelse
            </div>
        </div>
        
        <form action="{{ route('objectives.comments.store', $obj) }}" method="POST" enctype="multipart/form-data" style="margin: 0; background: #f8fafc; padding: 1.5rem; border-radius: 8px; border: 1px solid #edf2f7;">
            @csrf
            <label style="display: block; margin-bottom: 0.5rem; font-weight: 700; color: #4a5568; font-size: 0.9rem;">Agregar Comentario o Avance</label>
            <textarea name="comment" rows="2" required placeholder="Escribí acá tu comentario..." style="width: 100%; padding: 0.75rem; border-radius: 8px; border: 1px solid #cbd5e0; margin-bottom: 0.8rem; resize: vertical;"></textarea>
            
            <div style="display: flex; justify-content: space-between; align-items: center;">
                <div style="position: relative; overflow: hidden; display: inline-block;">
                    <button type="button" class="btn" style="background: white; border: 1px solid #cbd5e0; color: #4a5568; padding: 0.4rem 0.8rem; font-size: 0.85rem; border-radius: 6px; display: flex; align-items: center; gap: 0.4rem;">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21.44 11.05l-9.19 9.19a6 6 0 0 1-8.49-8.49l9.19-9.19a4 4 0 0 1 5.66 5.66l-9.2 9.19a2 2 0 0 1-2.83-2.83l8.49-8.48"></path></svg>
                        Adjuntar archivo
                    </button>
                    <input type="file" name="attachment" style="position: absolute; top: 0; left: 0; opacity: 0; width: 100%; height: 100%; cursor: pointer;" onchange="document.getElementById('file-name-{{ $obj->id }}').innerText = this.files.length > 0 ? this.files[0].name : '';">
                </div>
                <button type="submit" class="btn btn-primary" style="padding: 0.5rem 1.2rem; font-size: 0.9rem;">
                    Enviar Comentario
                </button>
            </div>
            <div id="file-name-{{ $obj->id }}" style="font-size: 0.8rem; color: #718096; margin-top: 0.5rem; font-weight: 600;"></div>
        </form>
    </div>
</div>

<script>
    function openNotesModal(id) { document.getElementById('notes-modal-' + id).style.display = 'flex'; }
    function closeNotesModal(id) { document.getElementById('notes-modal-' + id).style.display = 'none'; }
</script>
