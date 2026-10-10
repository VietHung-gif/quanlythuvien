<!-- COMPONENT NGĂN KÉO GIỎ SÁCH (SLIDE-OVER CART DRAWER) & TOAST & MODAL THÔNG BÁO -->
<div x-data="cartDrawerHandler()" 
     x-init="initCart()"
     @open-cart.window="isOpen = true"
     @cart-add.window="addItem($event.detail.bookId)"
     class="font-sans">

    <!-- 2. NGĂN KÉO GIỎ SÁCH (OFFCANVAS SLIDE-OVER DRAWER) -->
    <div x-show="isOpen" 
         x-cloak
         class="fixed inset-0 z-50 overflow-hidden" 
         role="dialog" 
         aria-modal="true">
        
        <!-- Lớp phủ mờ (Backdrop) -->
        <div x-show="isOpen"
             x-transition:enter="ease-in-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="ease-in-out duration-300"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             @click="isOpen = false"
             class="fixed inset-0 bg-slate-900/40 backdrop-blur-xs transition-opacity"></div>

        <div class="fixed inset-y-0 right-0 max-w-full flex pl-10">
            <div x-show="isOpen"
                 x-transition:enter="transform transition ease-in-out duration-300 sm:duration-400"
                 x-transition:enter-start="translate-x-full"
                 x-transition:enter-end="translate-x-0"
                 x-transition:leave="transform transition ease-in-out duration-300 sm:duration-400"
                 x-transition:leave-start="translate-x-0"
                 x-transition:leave-end="translate-x-full"
                 class="w-screen max-w-md bg-white border-l border-slate-200/90 text-slate-700 shadow-2xl flex flex-col">
                
                <!-- Header của Ngăn Kéo -->
                <div class="p-5 bg-slate-50/80 border-b border-slate-200/80 flex items-center justify-between shrink-0">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-xl bg-indigo-50 border border-indigo-200 flex items-center justify-center text-indigo-600 shadow-xs">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                            </svg>
                        </div>
                        <div>
                            <h2 class="font-['Orbitron'] font-bold text-base text-slate-900 tracking-wide flex items-center gap-2">
                                GIỎ SÁCH CỦA BẠN
                            </h2>
                            <p class="text-xs text-slate-500 font-mono">
                                Có <span class="text-indigo-600 font-bold" x-text="totalItems"></span> đầu sách đang lưu
                            </p>
                        </div>
                    </div>

                    <div class="flex items-center gap-2">
                        <template x-if="items.length > 0">
                            <button type="button" 
                                    @click="clearCart()" 
                                    class="text-[11px] text-slate-500 hover:text-rose-600 font-mono transition px-2 py-1 rounded hover:bg-slate-100"
                                    title="Xóa tất cả sách trong giỏ">
                                Xóa hết
                            </button>
                        </template>
                        <button type="button" 
                                @click="isOpen = false"
                                class="w-8 h-8 rounded-lg bg-slate-100 text-slate-500 hover:text-slate-900 hover:bg-slate-200 flex items-center justify-center transition">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- Thanh Chọn Tất Cả -->
                <template x-if="items.length > 0">
                    <div class="px-5 py-2.5 bg-slate-50/60 border-b border-slate-200/80 flex items-center justify-between text-xs shrink-0">
                        <label class="flex items-center gap-2 cursor-pointer select-none">
                            <input type="checkbox" 
                                   :checked="isAllSelected" 
                                   @change="toggleSelectAll($event.target.checked)"
                                   class="w-4 h-4 rounded bg-white border-slate-300 text-indigo-600 focus:ring-indigo-500 cursor-pointer">
                            <span class="font-medium text-slate-700">
                                Chọn tất cả (<span x-text="items.length"></span> đầu sách)
                            </span>
                        </label>
                        <span class="text-[11px] font-mono text-indigo-600 font-semibold" x-text="'Đã tích chọn: ' + selectedCount"></span>
                    </div>
                </template>

                <!-- Danh Sách Sách Trong Giỏ (Scrollable List) -->
                <div class="flex-1 overflow-y-auto p-5 space-y-4 custom-scrollbar">
                    <!-- Trạng thái trống -->
                    <template x-if="items.length === 0">
                        <div class="h-full flex flex-col items-center justify-center text-center py-16 text-slate-400">
                            <div class="w-20 h-20 rounded-2xl bg-slate-50 border border-slate-200 flex items-center justify-center text-indigo-500/60 mb-4 shadow-xs">
                                <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                                </svg>
                            </div>
                            <h3 class="font-['Orbitron'] font-bold text-slate-900 text-base mb-1">GIỎ SÁCH ĐANG TRỐNG</h3>
                            <p class="text-xs text-slate-500 max-w-xs mb-6 leading-relaxed">
                                Hãy dạo quanh kho lưu trữ và bấm "Thêm giỏ sách" để tích lũy những cuốn sách bạn yêu thích!
                            </p>
                            <a href="{{ route('books.index') }}" 
                               @click="isOpen = false"
                               class="bg-indigo-600 hover:bg-indigo-700 text-white px-5 py-2.5 rounded-xl font-['Orbitron'] font-bold text-xs uppercase tracking-wider transition shadow-sm">
                                &larr; KHÁM PHÁ KHO SÁCH NGAY
                            </a>
                        </div>
                    </template>

                    <!-- Danh sách các thẻ sách -->
                    <template x-for="item in items" :key="item.id">
                        <div class="p-3.5 rounded-2xl bg-white border transition-all duration-200 flex gap-3.5 items-start group"
                             :class="item.selected ? 'border-indigo-400 shadow-sm ring-1 ring-indigo-400/20' : 'border-slate-200/80 opacity-80'">
                            
                            <!-- Checkbox chọn cuốn -->
                            <div class="pt-2">
                                <input type="checkbox" 
                                       :checked="item.selected" 
                                       @change="toggleSelectItem(item.id, $event.target.checked)"
                                       class="w-4 h-4 rounded bg-white border-slate-300 text-indigo-600 focus:ring-indigo-500 cursor-pointer">
                            </div>

                            <!-- Ảnh bìa thu nhỏ -->
                            <div class="w-16 h-22 rounded-lg bg-slate-100 overflow-hidden shrink-0 border border-slate-200 shadow-xs relative">
                                <img :src="item.cover_url" 
                                     :alt="item.title"
                                     class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
                                     onerror="if(!this.dataset.triedFallback){ this.dataset.triedFallback='1'; this.src='{{ asset('images/covers/default.svg') }}'; }">
                            </div>

                            <!-- Thông tin chi tiết sách -->
                            <div class="flex-1 min-w-0">
                                <div class="flex items-start justify-between gap-1">
                                    <h4 class="font-bold text-slate-900 text-xs sm:text-sm line-clamp-2 leading-snug group-hover:text-indigo-600 transition"
                                        x-text="item.title"></h4>
                                    <!-- Nút icon thùng rác -->
                                    <button type="button" 
                                            @click="removeItem(item.id)"
                                            title="Xóa cuốn sách này khỏi giỏ"
                                            class="text-slate-400 hover:text-rose-600 p-1 rounded transition shrink-0">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                </div>

                                <div class="text-[11px] text-slate-500 mt-1 flex items-center gap-1.5">
                                    <span>Tác giả:</span>
                                    <span class="text-slate-700 font-medium truncate" x-text="item.author"></span>
                                </div>

                                <div class="text-[10px] text-slate-400 mt-0.5 truncate font-mono">
                                    <span x-text="item.publisher || 'NXB Trẻ'"></span>
                                    <template x-if="item.publish_year">
                                        <span x-text="' • ' + item.publish_year"></span>
                                    </template>
                                </div>

                                <!-- Bộ nút tăng / giảm số lượng -->
                                <div class="mt-3 flex items-center justify-between pt-2 border-t border-slate-100">
                                    <div class="flex items-center border border-slate-200 rounded-lg overflow-hidden bg-slate-50">
                                        <button type="button" 
                                                @click="changeQuantity(item.id, item.quantity - 1)"
                                                :disabled="item.quantity <= 1"
                                                class="w-7 h-7 flex items-center justify-center text-slate-500 hover:text-slate-900 hover:bg-slate-100 disabled:opacity-30 disabled:hover:bg-transparent transition text-xs font-bold">
                                            -
                                        </button>
                                        <span class="w-9 text-center text-xs font-mono font-bold text-indigo-700" 
                                              x-text="item.quantity"></span>
                                        <button type="button" 
                                                @click="changeQuantity(item.id, item.quantity + 1)"
                                                :disabled="item.quantity >= item.max_quantity"
                                                class="w-7 h-7 flex items-center justify-center text-slate-500 hover:text-slate-900 hover:bg-slate-100 disabled:opacity-30 disabled:hover:bg-transparent transition text-xs font-bold">
                                            +
                                        </button>
                                    </div>

                                    <div class="text-[10px] font-mono text-slate-500">
                                        Kho: <span class="text-emerald-700 font-bold" x-text="item.max_quantity + ' cuốn'"></span>
                                    </div                <!-- Footer Chân Giỏ Sách: Tổng kết, Đặc quyền VIP & Nút Đăng Ký Mượn -->
                <template x-if="items.length > 0">
                    <div class="p-5 bg-slate-50/95 border-t border-slate-200/90 shrink-0 space-y-4">
                        
                        <!-- DÒNG TỔNG KẾT TÌNH TRẠNG GIỎ SÁCH & HẠN MỨC GÓI -->
                        <div class="p-3.5 rounded-2xl bg-white border border-slate-200/90 shadow-2xs space-y-2 text-xs">
                            <div class="flex items-center justify-between font-mono">
                                <span class="text-slate-500 font-bold uppercase text-[11px]">Sách đã chọn:</span>
                                <div class="text-right">
                                    <span class="font-['Orbitron'] font-bold text-base" 
                                          :class="(!isVip && totalSelectedBooks > 3) ? 'text-rose-600' : 'text-indigo-600'" 
                                          x-text="totalSelectedBooks"></span>
                                    <span class="text-slate-600 font-sans"> / <strong x-text="maxLimit"></strong> cuốn</span>
                                    <span class="text-[10px] font-sans px-1.5 py-0.5 rounded font-black ml-1 uppercase"
                                          :class="isVip ? 'bg-amber-100 text-amber-900 border border-amber-300' : 'bg-slate-100 text-slate-700'">
                                        <span x-text="isVip ? '👑 VIP (15)' : 'STANDARD (3)'"></span>
                                    </span>
                                </div>
                            </div>

                            <!-- DÒNG TIỀN ĐẶT CỌC SÁCH -->
                            <div class="flex items-center justify-between pt-2 border-t border-slate-100">
                                <span class="text-slate-500 font-bold uppercase text-[11px]">Tiền cọc sách:</span>
                                <div class="text-right">
                                    <template x-if="isVip">
                                        <span class="font-bold text-emerald-600 flex items-center gap-1">
                                            <span class="font-mono text-sm font-black">0đ</span>
                                            <span class="text-[10px] bg-emerald-100 text-emerald-800 px-1.5 py-0.5 rounded font-black">
                                                (Đặc quyền VIP miễn cọc)
                                            </span>
                                        </span>
                                    </template>
                                    <template x-if="!isVip">
                                        <div class="text-right">
                                            <span class="font-mono font-bold text-slate-900">
                                                <span x-text="formatCurrency(totalSelectedBooks * 50000)"></span> đ
                                            </span>
                                            <span class="text-[10px] text-slate-400 block">
                                                (Bắt buộc 50.000đ/cuốn)
                                            </span>
                                        </div>
                                    </template>
                                </div>
                            </div>
                        </div>

                        <!-- CẢNH BÁO KHI ĐỘC GIẢ THƯỜNG CHỌN > 3 CUỐN (GỢI Ý NÂNG CẤP VIP) -->
                        <template x-if="!isVip && totalSelectedBooks > 3">
                            <div class="p-3.5 rounded-2xl bg-amber-50 border-2 border-amber-300 text-xs text-amber-950 space-y-2">
                                <div class="font-black flex items-center gap-1.5 text-amber-900">
                                    <span>⚡</span>
                                    <span>VƯỢT HẠN MỨC GÓI TIÊU CHUẨN (TỐI ĐA 3 CUỐN)</span>
                                </div>
                                <p class="text-[11px] text-slate-700 leading-snug">
                                    Bạn đang chọn <strong class="text-amber-900 font-bold" x-text="totalSelectedBooks"></strong> cuốn. Gói Standard Member chỉ cho phép mượn tối đa 3 cuốn/lần.
                                </p>
                                <a href="{{ route('membership.index') }}" 
                                   class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-black text-xs uppercase tracking-wider transition shadow-2xs">
                                    <span>👑 Nâng cấp Scholar VIP để mượn tối đa 15 cuốn cùng lúc!</span>
                                    <span>&rarr;</span>
                                </a>
                            </div>
                        </template>

                        <!-- ĐẶC QUYỀN VIP: LỰA CHỌN PHƯƠNG THỨC NHẬN SÁCH -->
                        <template x-if="isVip">
                            <div class="p-3.5 rounded-2xl bg-indigo-50/80 border-2 border-indigo-200 text-xs space-y-2.5">
                                <div class="flex items-center justify-between font-black text-indigo-950 uppercase tracking-tight text-[11px]">
                                    <span class="flex items-center gap-1.5">
                                        <span>🚀</span>
                                        <span>HÌNH THỨC NHẬN SÁCH ĐẶC QUYỀN VIP</span>
                                    </span>
                                </div>

                                <div class="space-y-2">
                                    <!-- LỰA CHỌN 1: LÀN ƯU TIÊN TẠI QUẦY -->
                                    <label class="flex items-start gap-2.5 p-2.5 rounded-xl bg-white border cursor-pointer transition select-none"
                                           :class="deliveryMethod === 'counter_priority' ? 'border-indigo-500 ring-2 ring-indigo-200 shadow-2xs bg-indigo-50/20' : 'border-slate-200'">
                                        <input type="radio" 
                                               name="delivery_method_drawer" 
                                               value="counter_priority" 
                                               x-model="deliveryMethod"
                                               class="mt-0.5 text-indigo-600 focus:ring-indigo-500">
                                        <div>
                                            <strong class="text-slate-900 block font-bold text-xs">🔘 Làn ưu tiên tại Quầy</strong>
                                            <span class="text-[11px] text-slate-500 block leading-tight mt-0.5">Thủ thư soạn sẵn trong 30 phút, đến quầy nhận ngay không chờ đợi</span>
                                        </div>
                                    </label>

                                    <!-- LỰA CHỌN 2: SHIP TẬN NHÀ / KTX -->
                                    <label class="flex items-start gap-2.5 p-2.5 rounded-xl bg-white border cursor-pointer transition select-none"
                                           :class="deliveryMethod === 'home_delivery' ? 'border-indigo-500 ring-2 ring-indigo-200 shadow-2xs bg-indigo-50/20' : 'border-slate-200'">
                                        <input type="radio" 
                                               name="delivery_method_drawer" 
                                               value="home_delivery" 
                                               x-model="deliveryMethod"
                                               class="mt-0.5 text-indigo-600 focus:ring-indigo-500">
                                        <div class="flex-1">
                                            <strong class="text-slate-900 block font-bold text-xs">🔘 Ship tận nhà / Ký túc xá</strong>
                                            <span class="text-[11px] text-slate-500 block leading-tight mt-0.5">Giao tận phòng KTX hoặc địa chỉ nhà riêng theo yêu cầu</span>
                                        </div>
                                    </label>
                                </div>

                                <!-- Ô NHẬP ĐỊA CHỈ NHẬN SÁCH KHI CHỌN SHIP TẬN NƠI -->
                                <template x-if="deliveryMethod === 'home_delivery'">
                                    <div class="pt-2 border-t border-indigo-100 space-y-1.5">
                                        <label class="block text-[11px] font-bold text-indigo-900 uppercase">
                                            Địa chỉ nhận sách (Phòng KTX / Nhà riêng) <span class="text-rose-500">*</span>:
                                        </label>
                                        <input type="text" 
                                               x-model="shippingAddress"
                                               placeholder="VD: Phòng 402 KTX Khu B hoặc 123 Đường Nguyễn Trãi..."
                                               class="w-full text-xs px-3 py-2 rounded-xl bg-white border border-slate-300 text-slate-800 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 outline-none">
                                    </div>
                                </template>
                            </div>
                        </template>

                        <!-- Ô NHẬP SĐT VÀ GHI CHÚ BỔ SUNG -->
                        <div class="space-y-2">
                            <input type="text" 
                                   x-model="borrowPhone"
                                   placeholder="Số điện thoại nhận sách & thông báo duyệt..."
                                   class="w-full text-xs px-3 py-2 rounded-lg bg-white border border-slate-200 text-slate-800 placeholder-slate-400 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 outline-none">
                            <input type="text" 
                                   x-model="borrowNotes"
                                   placeholder="Ghi chú thêm cho thủ thư (tùy chọn)..."
                                   class="w-full text-xs px-3 py-2 rounded-lg bg-white border border-slate-200 text-slate-800 placeholder-slate-400 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 outline-none">
                        </div>

                        <!-- BANNER CẢNH BÁO KHI CÒN NỢ TIỀN PHẠT -->
                        <template x-if="unpaidFine > 0">
                            <div class="p-3.5 rounded-xl bg-rose-50 border-2 border-rose-300 shadow-xs text-rose-800 text-xs flex items-start gap-3">
                                <span class="text-lg shrink-0">⚠️</span>
                                <div class="space-y-1">
                                    <div class="font-['Orbitron'] font-bold text-rose-700 tracking-wide text-xs">
                                        BẠN ĐANG CÒN NỢ TIỀN PHẠT: <span x-text="formatCurrency(unpaidFine)"></span> VNĐ
                                    </div>
                                    <div class="text-[11px] text-slate-600 leading-snug">
                                        Hãy thanh toán tại quầy để mở khóa chức năng mượn sách.
                                    </div>
                                </div>
                            </div>
                        </template>

                        <!-- NÚT NỔI BẬT "ĐĂNG KÝ MƯỢN SÁCH" -->
                        <button type="button" 
                                @click="checkoutCart()"
                                :disabled="isSubmitting || totalSelectedBooks === 0 || unpaidFine > 0 || (!isVip && totalSelectedBooks > 3)"
                                class="w-full py-3.5 rounded-xl font-['Orbitron'] font-bold text-xs uppercase tracking-wider flex items-center justify-center gap-2 transition"
                                :class="(unpaidFine > 0 || (!isVip && totalSelectedBooks > 3)) 
                                    ? 'bg-slate-100 text-slate-400 border border-slate-200 cursor-not-allowed opacity-80' 
                                    : 'bg-indigo-600 hover:bg-indigo-700 text-white shadow-sm disabled:opacity-50 disabled:cursor-not-allowed'">
                            
                            <template x-if="unpaidFine > 0">
                                <div class="flex items-center gap-2 text-rose-600">
                                    <span>TÀI KHOẢN ĐANG BỊ KHÓA MƯỢN SÁCH</span>
                                </div>
                            </template>

                            <template x-if="unpaidFine <= 0 && !isVip && totalSelectedBooks > 3">
                                <div class="flex items-center gap-2 text-amber-700">
                                    <span>VƯỢT HẠN MỨC (3 CUỐN) — HÃY NÂNG CẤP VIP</span>
                                </div>
                            </template>

                            <template x-if="unpaidFine <= 0 && (isVip || totalSelectedBooks <= 3) && !isSubmitting">
                                <div class="flex items-center gap-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <span x-text="isVip ? 'ĐĂNG KÝ MƯỢN SÁCH (ĐẶC QUYỀN VIP)' : 'ĐĂNG KÝ MƯỢN SÁCH NGAY'"></span>
                                </div>
                            </template>

                            <template x-if="unpaidFine <= 0 && isSubmitting">
                                <div class="flex items-center gap-2 text-white">
                                    <svg class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    <span>ĐANG TẠO PHIẾU MƯỢN...</span>
                                </div>
                            </template>
                        </button>
                    </div>
                </template>

            </div>
        </div>
    </div>

    <!-- 3. MODAL THÔNG BÁO CẢNH BÁO KHI SÁCH HẾT HÀNG (OUT OF STOCK ALERT MODAL) -->
    <div x-show="alertModalOpen" 
         x-cloak
         class="fixed inset-0 z-50 overflow-y-auto" 
         role="dialog" 
         aria-modal="true">
        <div class="flex items-center justify-center min-h-screen px-4 text-center">
            <!-- Backdrop -->
            <div x-show="alertModalOpen"
                 x-transition:enter="ease-out duration-300"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="ease-in duration-200"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 @click="alertModalOpen = false"
                 class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm transition-opacity"></div>

            <div x-show="alertModalOpen"
                 x-transition:enter="ease-out duration-300"
                 x-transition:enter-start="opacity-0 scale-95 translate-y-4"
                 x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                 x-transition:leave="ease-in duration-200"
                 x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                 x-transition:leave-end="opacity-0 scale-95 translate-y-4"
                 class="inline-block bg-white rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:max-w-md w-full border border-slate-200 p-6 relative z-10">
                
                <div class="flex items-center gap-3 border-b border-slate-100 pb-4 mb-4">
                    <div class="w-10 h-10 rounded-xl bg-rose-50 border border-rose-200 flex items-center justify-center text-rose-600 shrink-0 shadow-xs">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="font-['Orbitron'] font-bold text-lg text-slate-900" x-text="alertTitle">
                            Thông báo
                        </h3>
                        <div class="text-[11px] font-mono text-rose-600 font-semibold">
                            TRẠNG THÁI: TẠM THỜI HẾT SÁCH TRONG KHO
                        </div>
                    </div>
                </div>

                <div class="text-sm text-slate-600 leading-relaxed mb-6">
                    <p x-html="alertMessage"></p>
                </div>

                <div class="flex justify-end">
                    <button type="button" 
                            @click="alertModalOpen = false"
                            class="bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold px-6 py-2.5 rounded-xl font-['Orbitron'] text-xs uppercase tracking-wider transition">
                        ĐÓNG
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- 4. TOAST THÔNG BÁO GÓC PHẢI MÀN HÌNH (TOAST NOTIFICATIONS) -->
    <div class="fixed top-6 right-6 z-50 flex flex-col gap-2.5 pointer-events-none max-w-sm w-full">
        <template x-for="toast in toasts" :key="toast.id">
            <div x-show="toast.visible"
                 x-transition:enter="transform ease-out duration-300 transition"
                 x-transition:enter-start="translate-y-2 opacity-0 sm:translate-y-0 sm:translate-x-4"
                 x-transition:enter-end="translate-y-0 opacity-100 sm:translate-x-0"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 class="pointer-events-auto p-4 rounded-xl shadow-lg border flex items-start gap-3 backdrop-blur-md transition"
                 :class="{
                     'bg-white border-l-4 border-l-emerald-500 border-slate-200 text-slate-800 shadow-md': toast.type === 'success',
                     'bg-white border-l-4 border-l-rose-500 border-slate-200 text-slate-800 shadow-md': toast.type === 'error',
                     'bg-white border-l-4 border-l-amber-500 border-slate-200 text-slate-800 shadow-md': toast.type === 'warning'
                 }">
                
                <div class="shrink-0 mt-0.5">
                    <template x-if="toast.type === 'success'">
                        <svg class="w-5 h-5 text-emerald-600" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                    </template>
                    <template x-if="toast.type === 'error'">
                        <svg class="w-5 h-5 text-rose-500" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                        </svg>
                    </template>
                    <template x-if="toast.type === 'warning'">
                        <svg class="w-5 h-5 text-amber-500" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                        </svg>
                    </template>
                </div>

                <div class="flex-1 text-xs">
                    <p class="font-bold font-['Orbitron'] text-slate-900" x-text="toast.title"></p>
                    <p class="mt-0.5 text-slate-600 leading-relaxed" x-html="toast.message"></p>
                    <template x-if="toast.actionUrl">
                        <a :href="toast.actionUrl" 
                           class="inline-block mt-2 font-bold text-indigo-600 hover:text-indigo-700 underline font-mono text-[11px]">
                            Xem chi tiết phiếu mượn &rarr;
                        </a>
                    </template>
                </div>

                <button type="button" 
                        @click="removeToast(toast.id)"
                        class="text-slate-400 hover:text-slate-700 transition shrink-0">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </template>
    </div>
</div>

<!-- SCRIPT XỬ LÝ TOÀN BỘ LOGIC GIỎ SÁCH & TOAST BẰNG ALPINE.JS -->
<script>
function cartDrawerHandler() {
    return {
        isOpen: false,
        items: [],
        totalItems: 0,
        totalSelected: 0,
        totalSelectedBooks: 0,
        borrowPhone: '',
        borrowNotes: '',
        isSubmitting: false,

        // Đặc quyền Scholar VIP & Giao hàng
        isVip: false,
        maxLimit: 3,
        deliveryMethod: 'counter_priority',
        shippingAddress: '',
        depositFee: 0,

        // Nợ tiền phạt
        unpaidFine: 0,
        formatCurrency(num) {
            return new Intl.NumberFormat('vi-VN').format(num || 0);
        },

        // Modal cảnh báo hết sách
        alertModalOpen: false,
        alertTitle: 'Thông báo',
        alertMessage: '',

        // Toasts
        toasts: [],
        csrfToken: document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',

        get isAllSelected() {
            return this.items.length > 0 && this.items.every(i => i.selected);
        },

        get selectedCount() {
            return this.items.filter(i => i.selected).length;
        },

        async initCart() {
            // Lấy dữ liệu giỏ sách từ Server Session
            await this.fetchCart();

            // Đăng ký hàm toàn cục window để gọi từ bất kỳ đâu (onclick="window.addToCart(bookId)")
            window.addToCart = (bookId) => this.addItem(bookId);
            window.openCartDrawer = () => { this.isOpen = true; };
        },

        async fetchCart() {
            try {
                const res = await fetch('{{ route('cart.index') }}', {
                    headers: { 'Accept': 'application/json' }
                });
                const data = await res.json();
                if (data.success) {
                    this.items = data.cart || [];
                    this.totalItems = data.total_items || 0;
                    this.totalSelected = data.total_selected || 0;
                    this.totalSelectedBooks = data.total_selected_books || 0;
                    this.unpaidFine = Number(data.unpaid_fine || 0);
                    this.isVip = Boolean(data.is_vip);
                    this.maxLimit = Number(data.max_limit || 3);
                    this.depositFee = Number(data.deposit_fee || 0);
                    if (data.delivery_address && !this.shippingAddress) {
                        this.shippingAddress = data.delivery_address;
                    }
                    if (data.phone_number && !this.borrowPhone) {
                        this.borrowPhone = data.phone_number;
                    }
                    window.cartCount = this.totalItems;
                    window.dispatchEvent(new CustomEvent('cart-updated', { detail: { count: this.totalItems } }));
                }
            } catch (err) {
                console.error('Lỗi tải giỏ sách:', err);
            }
        },

        // BƯỚC 1: Thêm sách vào giỏ & Kiểm tra tồn kho tức thì
        async addItem(bookId) {
            try {
                const res = await fetch('{{ route('cart.add') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': this.csrfToken
                    },
                    body: JSON.stringify({ book_id: bookId })
                });

                const data = await res.json();

                // VẤN ĐỀ 2: Bị chặn do nợ tiền phạt chưa thanh toán
                if (data.has_unpaid_fine) {
                    this.unpaidFine = Number(data.unpaid_fine || this.unpaidFine);
                    this.showToast('Chặn mượn sách', data.message || 'Bạn đang còn nợ tiền phạt!', 'error');
                    return;
                }

                // Trường hợp 1: SÁCH ĐÃ HẾT TỒN KHO (quantity <= 0)
                if (data.out_of_stock) {
                    this.alertTitle = data.title || 'Thông báo';
                    this.alertMessage = 'Hiện tại cuốn sách này <strong>đã được mượn hết</strong>. Bạn vui lòng chọn một cuốn sách khác nhé!';
                    this.alertModalOpen = true;
                    return;
                }

                // Trường hợp 2: CÒN HÀNG - THÊM THÀNH CÔNG
                if (data.success) {
                    this.items = data.cart || [];
                    this.totalItems = data.total_items || 0;
                    await this.fetchCart();
                    this.showToast('Thành công', 'Sách đã được thêm vào giỏ', 'success');
                } else {
                    this.showToast('Thông báo', data.message || 'Không thể thêm sách vào giỏ.', 'warning');
                }
            } catch (err) {
                console.error('Lỗi thêm giỏ sách:', err);
                this.showToast('Lỗi', 'Có lỗi xảy ra khi thêm sách vào giỏ.', 'error');
            }
        },

        async changeQuantity(bookId, newQty) {
            if (newQty < 1) return;
            try {
                const res = await fetch('{{ route('cart.update-quantity') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': this.csrfToken
                    },
                    body: JSON.stringify({ book_id: bookId, quantity: newQty })
                });
                const data = await res.json();
                if (data.success) {
                    this.items = data.cart || [];
                    this.totalItems = data.total_items || 0;
                    this.totalSelected = data.total_selected || 0;
                    this.totalSelectedBooks = data.total_selected_books || 0;
                } else if (data.message) {
                    this.showToast('Cảnh báo tồn kho', data.message, 'warning');
                }
            } catch (err) {
                console.error('Lỗi cập nhật số lượng:', err);
            }
        },

        async toggleSelectItem(bookId, isSelected) {
            try {
                const res = await fetch('{{ route('cart.toggle-select') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': this.csrfToken
                    },
                    body: JSON.stringify({ book_id: bookId, selected: isSelected })
                });
                const data = await res.json();
                if (data.success) {
                    this.items = data.cart || [];
                    this.totalSelected = data.total_selected || 0;
                    this.totalSelectedBooks = data.total_selected_books || 0;
                }
            } catch (err) {
                console.error('Lỗi toggle item:', err);
            }
        },

        async toggleSelectAll(isSelected) {
            try {
                const res = await fetch('{{ route('cart.toggle-select') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': this.csrfToken
                    },
                    body: JSON.stringify({ book_id: 'all', selected: isSelected })
                });
                const data = await res.json();
                if (data.success) {
                    this.items = data.cart || [];
                    this.totalSelected = data.total_selected || 0;
                    this.totalSelectedBooks = data.total_selected_books || 0;
                }
            } catch (err) {
                console.error('Lỗi toggle all:', err);
            }
        },

        async removeItem(bookId) {
            try {
                const res = await fetch('{{ url('/cart/remove') }}/' + bookId, {
                    method: 'DELETE',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': this.csrfToken
                    }
                });
                const data = await res.json();
                if (data.success) {
                    this.items = data.cart || [];
                    this.totalItems = data.total_items || 0;
                    this.totalSelected = data.total_selected || 0;
                    this.totalSelectedBooks = data.total_selected_books || 0;
                    this.showToast('Thông báo', 'Đã xóa cuốn sách khỏi giỏ hàng', 'warning');
                }
            } catch (err) {
                console.error('Lỗi xóa khỏi giỏ:', err);
            }
        },

        async clearCart() {
            if (!confirm('Bạn có chắc chắn muốn xóa tất cả sách khỏi giỏ hàng?')) return;
            try {
                const res = await fetch('{{ route('cart.clear') }}', {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': this.csrfToken
                    }
                });
                const data = await res.json();
                if (data.success) {
                    this.items = [];
                    this.totalItems = 0;
                    this.totalSelected = 0;
                    this.totalSelectedBooks = 0;
                    this.showToast('Thông báo', 'Đã làm rỗng giỏ sách', 'warning');
                }
            } catch (err) {
                console.error('Lỗi xóa giỏ:', err);
            }
        },

        // BƯỚC 2: Đăng ký mượn sách & Tạo phiếu mượn
        async checkoutCart() {
            // VẤN ĐỀ 2: Chặn ngay tại Client nếu có nợ phạt
            if (this.unpaidFine > 0) {
                this.showToast('Chặn mượn sách', 'Tài khoản của bạn đang có tiền phạt chưa thanh toán (' + this.formatCurrency(this.unpaidFine) + ' VNĐ). Vui lòng gặp thủ thư thanh toán đủ tiền phạt để tiếp tục mượn sách!', 'error');
                return;
            }

            if (this.totalSelectedBooks <= 0) {
                this.showToast('Lỗi', 'Vui lòng chọn ít nhất 1 cuốn sách để mượn!', 'error');
                return;
            }

            // KIỂM TRA HẠN MỨC GÓI THÀNH VIÊN
            if (!this.isVip && this.totalSelectedBooks > 3) {
                this.showToast('Vượt hạn mức', 'Gói Standard chỉ cho phép mượn tối đa 3 cuốn/lần. Nâng cấp Scholar VIP để mượn tối đa 15 cuốn cùng lúc!', 'warning', '{{ route('membership.index') }}');
                return;
            }

            // KIỂM TRA ĐỊA CHỈ NẾU CHỌN SHIP TẬN NƠI
            if (this.isVip && this.deliveryMethod === 'home_delivery' && !this.shippingAddress.trim()) {
                this.showToast('Thiếu địa chỉ', 'Vui lòng nhập địa chỉ nhận sách tận nhà / KTX!', 'warning');
                return;
            }

            this.isSubmitting = true;

            try {
                const res = await fetch('{{ route('cart.checkout') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': this.csrfToken
                    },
                    body: JSON.stringify({
                        phone: this.borrowPhone,
                        notes: this.borrowNotes,
                        delivery_method: this.isVip ? this.deliveryMethod : 'counter_normal',
                        shipping_address: (this.isVip && this.deliveryMethod === 'home_delivery') ? this.shippingAddress : null,
                    })
                });

                const data = await res.json();

                if (res.status === 401 || data.login_required) {
                    alert('Bạn cần đăng nhập để đăng ký mượn sách!');
                    window.location.href = data.login_url || '{{ route('login') }}';
                    return;
                }

                // VẤN ĐỀ 2: Xử lý khi Server báo có nợ tiền phạt chưa thanh toán
                if (data.has_unpaid_fine) {
                    this.unpaidFine = Number(data.unpaid_fine || this.unpaidFine);
                    this.showToast('Chặn mượn sách', data.message, 'error');
                    return;
                }

                if (data.success) {
                    this.items = data.cart || [];
                    this.totalItems = data.total_items || 0;
                    this.totalSelected = 0;
                    this.totalSelectedBooks = 0;
                    this.isOpen = false;
                    window.cartCount = this.totalItems;
                    window.dispatchEvent(new CustomEvent('cart-updated', { detail: { count: this.totalItems } }));

                    // Hiển thị Toast thông báo thành công chuẩn đề bài
                    this.showToast(
                        'Thành công',
                        data.message || 'Đăng ký mượn sách thành công! Bạn sẽ nhận được thông báo ngay khi có kết quả.',
                        'success',
                        data.tracking_url
                    );

                    // Tự động chuyển hướng sau 2 giây
                    setTimeout(() => {
                        window.location.href = data.tracking_url;
                    }, 2000);
                } else {
                    this.showToast('Không thể mượn sách', data.message || 'Có lỗi xảy ra khi tạo phiếu mượn.', 'error');
                }
            } catch (err) {
                console.error('Lỗi đăng ký mượn:', err);
                this.showToast('Lỗi', 'Không thể kết nối đến máy chủ. Vui lòng thử lại!', 'error');
            } finally {
                this.isSubmitting = false;
            }
        },

        showToast(title, message, type = 'success', actionUrl = null) {
            const id = Date.now();
            this.toasts.push({
                id,
                title,
                message,
                type,
                actionUrl,
                visible: true
            });

            setTimeout(() => {
                this.removeToast(id);
            }, 4500);
        },

        removeToast(id) {
            const index = this.toasts.findIndex(t => t.id === id);
            if (index !== -1) {
                this.toasts[index].visible = false;
                setTimeout(() => {
                    this.toasts = this.toasts.filter(t => t.id !== id);
                }, 300);
            }
        }
    };
}
</script>
