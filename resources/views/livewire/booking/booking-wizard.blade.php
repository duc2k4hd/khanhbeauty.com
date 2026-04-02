<div class="max-w-4xl mx-auto bg-white rounded-[48px] shadow-[0_32px_80px_rgba(183,110,121,0.15)] overflow-hidden">
    {{-- Header --}}
    <div class="bg-rose-gold text-white p-8 md:p-12 text-center">
        <h2 class="font-heading text-3xl md:text-4xl mb-4">Đặt lịch làm đẹp</h2>
        <p class="text-pink-100 text-sm font-elegant italic tracking-widest">Chỉ mất 2 phút để đặt hẹn với chuyên gia của chúng tôi</p>
        
        {{-- Progress Bar --}}
        <div class="flex justify-between items-center mt-10 max-w-lg mx-auto relative">
            <div class="absolute h-0.5 bg-pink-200/30 left-0 right-0 top-1/2 -translate-y-1/2 z-0"></div>
            <div class="absolute h-0.5 bg-white left-0 top-1/2 -translate-y-1/2 z-0 transition-all duration-500" style="width: {{ ($step - 1) * 33.33 }}%"></div>
            
            @for ($i = 1; $i <= 4; $i++)
                <div class="relative z-10 w-10 h-10 rounded-full flex items-center justify-center font-bold text-sm transition-all duration-500 {{ $step >= $i ? 'bg-white text-rose-gold' : 'bg-rose-gold/50 text-white' }} ring-4 {{ $step == $i ? 'ring-white/20' : 'ring-transparent' }}">
                    {{ $i }}
                </div>
            @endfor
        </div>
    </div>

    {{-- Content Area --}}
    <div class="p-8 md:p-16 min-h-[400px]">
        @if (session()->has('message'))
            <div class="bg-green-50 text-green-700 p-8 rounded-3xl text-center flex flex-col items-center gap-6 animate-fade-in">
                <div class="w-16 h-16 bg-green-500 text-white rounded-full flex items-center justify-center text-3xl">✓</div>
                <p class="text-xl font-heading">{{ session('message') }}</p>
                <button wire:click="$refresh" class="text-green-700 border-b border-green-700 pb-1 text-sm font-semibold tracking-widest uppercase">Trở về</button>
            </div>
        @else
            {{-- Step 1: Chọn dịch vụ --}}
            @if ($step == 1)
                <div class="animate-fade-in">
                    <h3 class="font-heading text-2xl text-text-dark mb-8 text-center uppercase tracking-widest">1. Chọn dịch vụ của bạn</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <div>
                            <label class="block text-[11px] font-bold uppercase tracking-widest text-text-light mb-3">Dịch vụ</label>
                            <select wire:model.live="selectedServiceId" class="w-full bg-cream-warm border border-pink-100 rounded-2xl px-6 py-4 focus:outline-none focus:ring-2 focus:ring-pink-300">
                                <option value="">--- Chọn dịch vụ ---</option>
                                @foreach($services as $service)
                                    <option value="{{ $service->id }}">{{ $service->name }}</option>
                                @endforeach
                            </select>
                            @error('selectedServiceId') <span class="text-pink-500 text-xs mt-2 block">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-[11px] font-bold uppercase tracking-widest text-text-light mb-3">Loại dịch vụ / Gói</label>
                            <select wire:model.live="selectedVariantId" class="w-full bg-cream-warm border border-pink-100 rounded-2xl px-6 py-4 focus:outline-none focus:ring-2 focus:ring-pink-300">
                                <option value="">--- Chọn gói cụ thể ---</option>
                                @if($selectedServiceId)
                                    @php $service = $services->find($selectedServiceId); @endphp
                                    @foreach($service->variants as $variant)
                                        <option value="{{ $variant->id }}">{{ $variant->name }} - {{ number_format($variant->price, 0, ',', '.') }}đ ({{ $variant->duration }} phút)</option>
                                    @endforeach
                                @endif
                            </select>
                            @error('selectedVariantId') <span class="text-pink-500 text-xs mt-2 block">{{ $message }}</span> @enderror
                        </div>
                    </div>
                </div>
            @endif

            {{-- Step 2: Chọn chuyên viên --}}
            @if ($step == 2)
                <div class="animate-fade-in">
                    <h3 class="font-heading text-2xl text-text-dark mb-8 text-center uppercase tracking-widest">2. Ai sẽ phục vụ bạn?</h3>
                    <div class="grid grid-cols-2 md:grid-cols-3 gap-6">
                        @forelse($staffs as $staff)
                            <div wire:click="$set('selectedStaffId', {{ $staff->id }})" class="cursor-pointer group relative">
                                <div class="aspect-square rounded-3xl overflow-hidden border-4 {{ $selectedStaffId == $staff->id ? 'border-pink-500' : 'border-pink-500/0 hover:border-pink-200' }} transition-all">
                                    <img src="{{ $staff->avatar ?? 'https://ui-avatars.com/api/?name='.urlencode($staff->full_name).'&background=FED7E2&color=ED64A6' }}" class="w-full h-full object-cover group-hover:scale-110 transition-all duration-700">
                                </div>
                                <div class="mt-4 text-center">
                                    <h4 class="font-heading text-lg text-text-dark">{{ $staff->full_name }}</h4>
                                    <span class="text-[10px] text-text-light uppercase tracking-widest">Chuyên gia Trang điểm</span>
                                </div>
                                @if($selectedStaffId == $staff->id)
                                    <div class="absolute top-4 right-4 bg-pink-500 text-white w-8 h-8 rounded-full flex items-center justify-center text-xs shadow-lg">✓</div>
                                @endif
                            </div>
                        @empty
                            <div class="col-span-full py-12 text-center text-text-light italic">Vui lòng chọn dịch vụ trước hoặc không tìm thấy chuyên viên cho dịch vụ này.</div>
                        @endforelse
                    </div>
                    @error('selectedStaffId') <span class="text-pink-500 text-xs mt-6 block text-center">{{ $message }}</span> @enderror
                </div>
            @endif

            {{-- Step 3: Chọn thời gian --}}
            @if ($step == 3)
                <div class="animate-fade-in">
                    <h3 class="font-heading text-2xl text-text-dark mb-8 text-center uppercase tracking-widest">3. Thời gian hẹn</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-10">
                        <div>
                            <label class="block text-[11px] font-bold uppercase tracking-widest text-text-light mb-3">Ngày hẹn</label>
                            <input type="date" wire:model.live="bookingDate" class="w-full bg-cream-warm border border-pink-100 rounded-2xl px-6 py-4 focus:outline-none focus:ring-2 focus:ring-pink-300">
                            @error('bookingDate') <span class="text-pink-500 text-xs mt-2 block">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-[11px] font-bold uppercase tracking-widest text-text-light mb-3">Giờ hẹn mong muốn</label>
                            <div class="grid grid-cols-3 gap-3">
                                @foreach($timeSlots as $slot)
                                    <button wire:click="$set('timeSlot', '{{ $slot }}')" class="py-3 text-xs rounded-xl border {{ $timeSlot == $slot ? 'bg-pink-500 border-pink-500 text-white' : 'bg-white border-pink-100 text-text-medium hover:border-pink-300' }} transition-all">
                                        {{ $slot }}
                                    </button>
                                @endforeach
                            </div>
                            @error('timeSlot') <span class="text-pink-500 text-xs mt-2 block">{{ $message }}</span> @enderror
                        </div>
                    </div>
                </div>
            @endif

            {{-- Step 4: Thông tin cá nhân --}}
            @if ($step == 4)
                <div class="animate-fade-in">
                    <h3 class="font-heading text-2xl text-text-dark mb-8 text-center uppercase tracking-widest">4. Chạm bước cuối cùng</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <div>
                            <label class="block text-[11px] font-bold uppercase tracking-widest text-text-light mb-3">Họ và tên *</label>
                            <input type="text" wire:model="name" placeholder="Vd: Nguyễn Văn A" class="w-full bg-cream-warm border border-pink-100 rounded-2xl px-6 py-4 focus:outline-none focus:ring-2 focus:ring-pink-300">
                            @error('name') <span class="text-pink-500 text-xs mt-2 block">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-[11px] font-bold uppercase tracking-widest text-text-light mb-3">Số điện thoại *</label>
                            <input type="tel" wire:model="phone" placeholder="0xxxxxxxxx" class="w-full bg-cream-warm border border-pink-100 rounded-2xl px-6 py-4 focus:outline-none focus:ring-2 focus:ring-pink-300">
                            @error('phone') <span class="text-pink-500 text-xs mt-2 block">{{ $message }}</span> @enderror
                        </div>
                        <div class="col-span-full">
                            <label class="block text-[11px] font-bold uppercase tracking-widest text-text-light mb-3">Ghi chú thêm</label>
                            <textarea wire:model="note" rows="3" placeholder="Ghi chú về sở thích, địa chỉ hoặc yêu cầu đặc biệt..." class="w-full bg-cream-warm border border-pink-100 rounded-2xl px-6 py-4 focus:outline-none focus:ring-2 focus:ring-pink-300"></textarea>
                        </div>
                    </div>
                </div>
            @endif

            {{-- Navigation Buttons --}}
            <div class="mt-16 flex justify-between items-center bg-cream-warm/50 -m-8 md:-m-16 p-8 md:px-16 md:py-8 border-t border-pink-100">
                @if ($step > 1)
                    <button wire:click="prevStep" class="text-text-medium text-sm font-semibold tracking-widest flex items-center gap-2 hover:text-pink-500 transition-all">
                        ← Quay lại
                    </button>
                @else
                    <div></div>
                @endif

                @if ($step < 4)
                    <button wire:click="nextStep" class="bg-rose-gold text-white px-10 py-4 rounded-full font-bold text-sm tracking-widest flex items-center gap-2 hover:bg-rose-gold/90 transition-all shadow-lg hover:-translate-y-1">
                        Tiếp tục →
                    </button>
                @else
                    <button wire:click="submit" class="bg-gradient-to-r from-pink-500 to-rose-gold text-white px-10 py-4 rounded-full font-bold text-sm tracking-widest flex items-center gap-2 hover:-translate-y-1 transition-all shadow-xl">
                        XÁC NHẬN ĐẶT LỊCH
                    </button>
                @endif
            </div>
        @endif
    </div>

    <style>
        .animate-fade-in { animation: fadeIn 0.5s ease-out; }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
    </style>
</div>
