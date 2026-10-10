<div class="bg-white border-2 border-slate-200 rounded-2xl p-5 shadow-xs hover:border-slate-300 transition flex flex-col sm:flex-row sm:items-center justify-between gap-4">
    <!-- Cột thông tin & Thanh tiến độ -->
    <div class="flex items-start gap-4 flex-1 min-w-0">
        <div class="w-12 h-12 rounded-2xl bg-slate-50 border border-slate-200 flex items-center justify-center text-2xl shrink-0 shadow-2xs">
            {{ $quest['icon'] }}
        </div>

        <div class="flex-1 min-w-0 space-y-1">
            <div class="flex items-center gap-2 flex-wrap">
                <h3 class="font-black text-sm text-slate-900 uppercase tracking-tight">
                    {{ $quest['title'] }}
                </h3>
                
                <!-- Badge EXP tương xứng theo độ khó -->
                <span class="{{ $quest['badge_color'] }} text-[11px] px-2.5 py-0.5 rounded-lg shrink-0 border border-transparent">
                    +{{ $quest['reward_exp'] }} EXP
                </span>

                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider bg-slate-100 px-2 py-0.5 rounded">
                    {{ $quest['difficulty_label'] }}
                </span>
            </div>

            <p class="text-xs text-slate-500 font-medium leading-relaxed">
                {{ $quest['description'] }}
            </p>

            <!-- Thanh tiến trình nhiệm vụ (Progress Bar) -->
            <div class="pt-1.5 max-w-sm">
                <div class="flex items-center justify-between text-[11px] font-bold text-slate-600 mb-1">
                    <span>Tiến độ: {{ $quest['current'] }}/{{ $quest['target'] }} {{ $quest['unit'] }}</span>
                    <span>{{ $quest['percent'] }}%</span>
                </div>
                <div class="h-2 bg-slate-100 rounded-full overflow-hidden border border-slate-200">
                    <div class="h-full rounded-full transition-all duration-500 
                                {{ $quest['difficulty'] === 'hard' 
                                    ? 'bg-gradient-to-r from-amber-500 to-red-500' 
                                    : ($quest['difficulty'] === 'medium' ? 'bg-blue-600' : 'bg-slate-700') }}" 
                         style="width: {{ $quest['percent'] }}%;"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Nút hành động (Action Button) -->
    <div class="shrink-0 flex items-center sm:self-center">
        @auth
            @if($quest['is_claimed'])
                <!-- Đã hoàn thành & nhận thưởng -->
                <span class="bg-emerald-50 text-emerald-700 border-2 border-emerald-200 font-bold px-4 py-2.5 rounded-xl text-xs flex items-center gap-1.5 shadow-2xs">
                    <span>✓</span> ĐÃ HOÀN THÀNH
                </span>
            @elseif($quest['is_completed'])
                <!-- Đã đủ điều kiện -> Nút nhận thưởng -->
                <button type="button"
                        @click="claimReward('{{ $quest['id'] }}', {{ $quest['reward_exp'] }}, '{{ $quest['title'] }}')"
                        :disabled="isSubmitting"
                        class="bg-emerald-600 hover:bg-emerald-700 text-white font-black text-xs uppercase tracking-wider px-5 py-2.5 rounded-xl shadow-xs hover:shadow-md transition cursor-pointer flex items-center gap-1.5 animate-pulse">
                    <span>🎁</span> NHẬN THƯỞNG (+{{ $quest['reward_exp'] }} EXP)
                </button>
            @else
                <!-- Chưa hoàn thành -> Nút LÀM NGAY / Điều hướng -->
                @if($quest['action_type'] === 'checkin')
                    <button type="button"
                            @click="doCheckin()"
                            :disabled="isSubmitting"
                            class="bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs uppercase px-4 py-2.5 rounded-xl shadow-xs transition cursor-pointer">
                        {{ $quest['action_text'] }}
                    </button>
                @elseif(!empty($quest['action_url']))
                    <a href="{{ $quest['action_url'] }}" 
                       class="bg-slate-100 hover:bg-blue-50 text-slate-700 hover:text-blue-700 font-bold text-xs uppercase px-4 py-2.5 rounded-xl border border-slate-200 hover:border-blue-300 transition flex items-center gap-1">
                        <span>LÀM NGAY</span>
                        <span>→</span>
                    </a>
                @else
                    <button type="button"
                            @click="doCheckin()"
                            :disabled="isSubmitting"
                            class="bg-slate-100 hover:bg-blue-50 text-slate-700 hover:text-blue-700 font-bold text-xs uppercase px-4 py-2.5 rounded-xl border border-slate-200 hover:border-blue-300 transition cursor-pointer">
                        {{ $quest['action_text'] }}
                    </button>
                @endif
            @endif
        @else
            <a href="{{ route('login') }}" 
               class="bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs uppercase px-4 py-2.5 rounded-xl border border-slate-200 transition">
                ĐĂNG NHẬP
            </a>
        @endauth
    </div>
</div>
