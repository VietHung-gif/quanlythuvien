<div class="bg-white border-2 border-slate-200 rounded-2xl p-5 shadow-xs hover:border-slate-300 transition flex flex-col sm:flex-row sm:items-center justify-between gap-4">
    <!-- Cột thông tin & Thanh tiến độ -->
    <div class="flex items-start gap-4 flex-1 min-w-0">
        <div class="w-12 h-12 rounded-2xl bg-slate-50 border border-slate-200 flex items-center justify-center shrink-0 shadow-2xs">
            @if(($quest['id'] ?? '') === 'daily_checkin')
                <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
            @elseif(($quest['id'] ?? '') === 'daily_view_books')
                <svg class="w-6 h-6 text-sky-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            @elseif(($quest['id'] ?? '') === 'medium_borrow_approved')
                <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
            @elseif(($quest['id'] ?? '') === 'medium_forum_engagement')
                <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
            @elseif(($quest['id'] ?? '') === 'hard_category_specialist')
                <svg class="w-6 h-6 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
            @elseif(($quest['id'] ?? '') === 'hard_five_clean_returns')
                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
            @elseif(($quest['id'] ?? '') === 'hard_streak_14_days')
                <svg class="w-6 h-6 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 18.657A8 8 0 016.343 7.343S7 9 9 10c0-2 .5-5 2.986-7C14 5 16.09 5.777 17.656 7.343A7.975 7.975 0 0120 13a7.975 7.975 0 01-2.343 5.657z"/></svg>
            @else
                <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            @endif
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
                    <svg class="w-3.5 h-3.5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                    <span>ĐÃ HOÀN THÀNH</span>
                </span>
            @elseif($quest['is_completed'])
                <!-- Đã đủ điều kiện -> Nút nhận thưởng -->
                <button type="button"
                        @click="claimReward('{{ $quest['id'] }}', {{ $quest['reward_exp'] }}, '{{ $quest['title'] }}')"
                        :disabled="isSubmitting"
                        class="bg-emerald-600 hover:bg-emerald-700 text-white font-black text-xs uppercase tracking-wider px-5 py-2.5 rounded-xl shadow-xs hover:shadow-md transition cursor-pointer flex items-center gap-1.5 animate-pulse">
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v13m0-13V4a2 2 0 112 2h-2zm0 0V4a2 2 0 10-2 2h2zm-7 5h14a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2z"/></svg>
                    <span>NHẬN THƯỞNG (+{{ $quest['reward_exp'] }} EXP)</span>
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
