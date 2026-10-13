@if ($errors->any())
    <div class="mb-6 rounded-md border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-600">
        <ul class="list-inside list-disc">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="bg-white rounded-lg border border-gray-200" x-data="productWizard()">
    
    <!-- Stepper UI (Clean & Professional) -->
    <div class="border-b border-gray-200 px-4 py-6 sm:px-8">
        <nav aria-label="Progress">
            <ol role="list" class="flex items-start">
                <template x-for="i in maxStep" :key="i">
                    <li class="relative flex-1 text-center">
                        <!-- Line connecting steps (don't show on last step) -->
                        <div x-show="i !== maxStep" class="absolute left-1/2 top-4 w-full flex items-center" aria-hidden="true">
                            <div class="h-0.5 w-full transition-colors duration-300" :class="step > i ? 'bg-brand-orange' : 'bg-gray-200'"></div>
                        </div>
                        
                        <button type="button" @click="step = i" class="relative z-10 flex flex-col items-center group focus:outline-none w-full">
                            <span class="flex h-8 w-8 items-center justify-center rounded-full transition-colors duration-300 mx-auto"
                                :class="step > i ? 'bg-brand-orange hover:bg-brand-orange/90' : (step === i ? 'border-2 border-brand-orange bg-white' : 'border-2 border-gray-300 bg-white group-hover:border-gray-400')">
                                <i x-show="step > i" class="fa-solid fa-check text-white text-xs" style="display: none;"></i>
                                <span x-show="step <= i" class="text-xs font-medium" :class="step === i ? 'text-brand-orange' : 'text-gray-500'" x-text="i"></span>
                            </span>
                            
                            <!-- Titles -->
                            <span class="mt-2 text-xs font-medium transition-colors duration-300" 
                                  :class="step === i ? 'text-brand-orange' : (step > i ? 'text-gray-900' : 'text-gray-400')"
                                  x-text="['Basic Info', 'Pricing', 'Attributes', 'Media', 'Visibility', 'Metrics'][i-1]">
                            </span>
                        </button>
                    </li>
                </template>
            </ol>
        </nav>
    </div>

    <div class="p-6 sm:p-8">
        <!-- Step 1: General Information -->
        <div x-show="step === 1" style="display: none;">
            <h3 class="text-base font-medium text-gray-900 mb-5">General Information</h3>
            
            <div class="grid gap-6 sm:grid-cols-2">
                <div class="sm:col-span-2">
                    <label class="block text-sm font-medium text-gray-700">Product Name <span class="text-red-500">*</span></label>
                    <input type="text" name="name" value="{{ old('name', $product->name ?? '') }}"
                        @input="delete errors.name"
                        :class="errors.name ? 'border-red-400 ring-1 ring-red-300' : 'border-gray-300'"
                        class="mt-1 block w-full rounded-md border px-3 py-2 text-sm focus:border-brand-orange focus:outline-none focus:ring-1 focus:ring-brand-orange">
                    <p x-show="errors.name" x-text="errors.name" class="mt-1 text-xs text-red-600" style="display:none;"></p>
                </div>

                <div class="sm:col-span-2">
                    <label class="block text-sm font-medium text-gray-700">Category <span class="text-red-500">*</span></label>
                    <select name="category_id" @change="delete errors.category_id"
                        :class="errors.category_id ? 'border-red-400 ring-1 ring-red-300' : 'border-gray-300'"
                        class="mt-1 block w-full rounded-md border px-3 py-2 text-sm focus:border-brand-orange focus:outline-none focus:ring-1 focus:ring-brand-orange bg-white">
                        <option value="">— Select Category —</option>
                        @foreach ($categoryGroups as $parent)
                            <optgroup label="{{ $parent->name }}">
                                <option value="{{ $parent->id }}" @selected(old('category_id', $product->category_id ?? '') == $parent->id)>{{ $parent->name }} (Main Category)</option>
                                @foreach ($parent->children as $child)
                                    <option value="{{ $child->id }}" @selected(old('category_id', $product->category_id ?? '') == $child->id)>&nbsp;&nbsp;&nbsp;&nbsp;↳ {{ $child->name }}</option>
                                @endforeach
                            </optgroup>
                        @endforeach
                    </select>
                    <p x-show="errors.category_id" x-text="errors.category_id" class="mt-1 text-xs text-red-600" style="display:none;"></p>
                </div>

                <div class="sm:col-span-2">
                    <label class="block text-sm font-medium text-gray-700">Brand</label>
                    <select name="brand_id" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-brand-orange focus:outline-none focus:ring-1 focus:ring-brand-orange bg-white">
                        <option value="">— No Brand —</option>
                        @foreach ($brands as $brand)
                            <option value="{{ $brand->id }}" @selected(old('brand_id', $product->brand_id ?? '') == $brand->id)>{{ $brand->name }}</option>
                        @endforeach
                    </select>
                    <p class="mt-1 text-xs text-gray-500">
                        Manage brands in <a href="{{ route('admin.brands.index') }}" class="text-brand-orange hover:underline">Brands</a>.
                    </p>
                </div>

                <div class="sm:col-span-2">
                    <label class="block text-sm font-medium text-gray-700">Short Description</label>
                    <textarea name="short_description" rows="2" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-brand-orange focus:outline-none focus:ring-1 focus:ring-brand-orange">{{ old('short_description', $product->short_description ?? '') }}</textarea>
                    <p class="mt-1 text-xs text-gray-500">Brief overview displayed next to the product image.</p>
                </div>

                <div class="sm:col-span-2" wire:ignore>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Full Description</label>
                    <!-- Hidden input to store Quill content -->
                    <input type="hidden" name="description" id="description-input" value="{{ old('description', $product->description ?? '') }}">
                    <div id="quill-editor" class="bg-white rounded-b-md border-gray-300" style="min-height: 200px;">
                        {!! old('description', $product->description ?? '') !!}
                    </div>
                </div>
            </div>
        </div>

        <!-- Step 2: Pricing & Inventory -->
        <div x-show="step === 2" style="display: none;" x-cloak>
            <h3 class="text-base font-medium text-gray-900 mb-5">Pricing & Inventory</h3>
            
            <div class="grid gap-8 sm:grid-cols-2">
                <div>
                    <h4 class="text-sm font-medium text-gray-900 mb-4 pb-2 border-b border-gray-100">Pricing</h4>
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Discount Price (৳) <span class="text-red-500">*</span></label>
                            <input type="number" step="0.01" name="price" value="{{ old('price', $product->price ?? '') }}"
                                @input="delete errors.price"
                                :class="errors.price ? 'border-red-400 ring-1 ring-red-300' : 'border-gray-300'"
                                class="mt-1 block w-full rounded-md border px-3 py-2 text-sm focus:border-brand-orange focus:outline-none focus:ring-1 focus:ring-brand-orange">
                            <p x-show="errors.price" x-text="errors.price" class="mt-1 text-xs text-red-600" style="display:none;"></p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Old Price (৳)</label>
                            <input type="number" step="0.01" name="old_price" value="{{ old('old_price', $product->old_price ?? '') }}"
                                class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-brand-orange focus:outline-none focus:ring-1 focus:ring-brand-orange">
                        </div>
                    </div>
                </div>

                <div>
                    <h4 class="text-sm font-medium text-gray-900 mb-4 pb-2 border-b border-gray-100">Inventory</h4>
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Stock Quantity <span class="text-red-500">*</span></label>
                            <input type="number" name="stock" value="{{ old('stock', $product->stock ?? 0) }}"
                                @input="delete errors.stock"
                                :class="errors.stock ? 'border-red-400 ring-1 ring-red-300' : 'border-gray-300'"
                                class="mt-1 block w-full rounded-md border px-3 py-2 text-sm focus:border-brand-orange focus:outline-none focus:ring-1 focus:ring-brand-orange">
                            <p x-show="errors.stock" x-text="errors.stock" class="mt-1 text-xs text-red-600" style="display:none;"></p>
                        </div>

                        <div x-data="skuChecker('{{ old('sku', $product->sku ?? '') }}', {{ isset($product) ? $product->id : 'null' }})">
                            <label class="block text-sm font-medium text-gray-700">SKU (Stock Keeping Unit)</label>
                            <div class="relative mt-1">
                                <input type="text" name="sku" x-model="sku" @input.debounce.500ms="checkSku" autocomplete="off"
                                    class="block w-full rounded-md border px-3 py-2 text-sm focus:outline-none focus:ring-1"
                                    :class="inputClass">
                                <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                    <i x-show="isChecking" class="fa-solid fa-spinner fa-spin text-gray-400"></i>
                                    <i x-show="!isChecking && isValid === true && sku.length > 0" class="fa-solid fa-check text-green-500"></i>
                                    <i x-show="!isChecking && isValid === false" class="fa-solid fa-xmark text-red-500"></i>
                                </div>
                            </div>
                            <p x-show="!isChecking && message" x-text="message" class="mt-1 text-xs" :class="isValid ? 'text-green-600' : 'text-red-600'"></p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Weight / Size</label>
                            <input type="text" name="weight" value="{{ old('weight', $product->weight ?? '') }}" placeholder="e.g. 1kg, 500ml, 400g"
                                class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-brand-orange focus:outline-none focus:ring-1 focus:ring-brand-orange">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Step 3: Product Attributes / Variants -->
        <div x-show="step === 3" style="display: none;" x-cloak>
            <div x-data="variantBuilder({{ isset($product) && $product->variants ? $product->variants->toJson() : '[]' }}, {{ isset($product) && $product->variants && $product->variants->count() > 0 ? 'true' : 'false' }}, {{ isset($product) && $product->show_variant_image ? 'true' : 'false' }})">
                
                <!-- Variable Product Mode Toggle Card -->
                <div class="flex items-center justify-between p-4 rounded-lg mb-4 transition-colors border" :class="hasVariants ? 'bg-blue-50/50 border-blue-200' : 'bg-gray-50 border-gray-200'">
                    <div class="flex items-start gap-3">
                        <div class="mt-0.5 text-lg" :class="hasVariants ? 'text-brand-orange' : 'text-gray-400'">
                            <i class="fa-solid fa-circle-info"></i>
                        </div>
                        <div>
                            <h4 class="text-sm font-semibold" :class="hasVariants ? 'text-brand-navy' : 'text-gray-700'">Variable Product Mode</h4>
                            <p class="text-xs text-gray-500 mt-1">Add variations like size, color, or material. Each variation can have its own SKU, price, and stock.</p>
                        </div>
                    </div>
                    <div class="ml-4 shrink-0">
                        <button type="button" @click="hasVariants = !hasVariants" :class="hasVariants ? 'bg-brand-orange' : 'bg-gray-200'" 
                            class="relative inline-flex h-6 w-11 shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-brand-orange focus:ring-offset-2">
                            <span aria-hidden="true" :class="hasVariants ? 'translate-x-5' : 'translate-x-0'" class="pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out"></span>
                        </button>
                        <input type="hidden" name="has_variants" :value="hasVariants ? 1 : 0">
                    </div>
                </div>

                <!-- Sub-options (only when variant mode ON) -->
                <div x-show="hasVariants" class="grid grid-cols-2 gap-3 mb-6">
                    <!-- Show Image Instead of Color -->
                    <div class="flex items-center justify-between p-3 bg-white border border-gray-200 rounded-lg">
                        <div>
                            <p class="text-xs font-semibold text-gray-700">Show Image Instead of Color</p>
                            <p class="text-xs text-gray-400 mt-0.5">Uses the variant image as selection button on frontend.</p>
                        </div>
                        <button type="button" @click="showVariantImage = !showVariantImage" :class="showVariantImage ? 'bg-brand-orange' : 'bg-gray-200'" 
                            class="ml-3 shrink-0 relative inline-flex h-5 w-10 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out">
                            <span :class="showVariantImage ? 'translate-x-5' : 'translate-x-0'" class="pointer-events-none inline-block h-4 w-4 transform rounded-full bg-white shadow transition duration-200"></span>
                        </button>
                        <input type="hidden" name="show_variant_image" :value="showVariantImage ? 1 : 0">
                    </div>
                    <!-- Convert & Optimize WebP -->
                    <div class="flex items-center justify-between p-3 bg-white border border-gray-200 rounded-lg">
                        <div>
                            <p class="text-xs font-semibold text-gray-700">Convert & Optimize (WebP)</p>
                            <p class="text-xs text-gray-400 mt-0.5">Auto-scale & convert variant images to lightweight WebP.</p>
                        </div>
                        <button type="button" @click="optimizeWebp = !optimizeWebp" :class="optimizeWebp ? 'bg-brand-orange' : 'bg-gray-200'" 
                            class="ml-3 shrink-0 relative inline-flex h-5 w-10 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out">
                            <span :class="optimizeWebp ? 'translate-x-5' : 'translate-x-0'" class="pointer-events-none inline-block h-4 w-4 transform rounded-full bg-white shadow transition duration-200"></span>
                        </button>
                        <input type="hidden" name="optimize_webp" :value="optimizeWebp ? 1 : 0">
                    </div>
                </div>

                <!-- Manually Add Variants -->
                <div x-show="hasVariants" class="space-y-4">
                    <div class="border-b border-gray-100 pb-2 mb-1">
                        <p class="text-xs font-semibold text-gray-600 uppercase tracking-wider">Manually Add Variants</p>
                        <p class="text-xs text-gray-400 mt-0.5">Select an option type (like Color or Size) and define each variant directly.</p>
                    </div>

                    <template x-for="(variant, index) in variants" :key="index">
                        <div class="border border-gray-200 rounded-xl bg-white shadow-sm overflow-hidden">
                            <!-- Variant Header -->
                            <div class="flex items-center justify-between px-4 py-3 bg-gray-50 border-b border-gray-100">
                                <span class="text-sm font-semibold text-brand-navy">Variant Attributes (e.g., Color + Size)</span>
                                <div class="flex items-center gap-2">
                                    <button type="button" @click="addOption(index)" class="text-xs font-medium text-brand-orange hover:text-brand-navy flex items-center gap-1">
                                        <i class="fa-solid fa-plus text-[10px]"></i> Add Attribute
                                    </button>
                                    <button type="button" @click="removeVariant(index)" class="text-gray-400 hover:text-red-500 p-1 ml-2">
                                        <i class="fa-solid fa-trash-can text-xs"></i>
                                    </button>
                                </div>
                            </div>

                            <div class="p-4 space-y-4">
                                <!-- Options -->
                                <div class="space-y-2">
                                    <template x-for="(opt, oIndex) in variant.options" :key="oIndex">
                                        <div class="flex items-center gap-2">
                                            <input type="text" :name="`variants[${index}][options][${oIndex}][name]`" x-model="opt.name" 
                                                placeholder="Option Name (e.g., Color, Size, Weight)" 
                                                class="flex-1 rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-brand-orange focus:outline-none focus:ring-1 focus:ring-brand-orange">
                                            <input type="text" :name="`variants[${index}][options][${oIndex}][value]`" x-model="opt.value" 
                                                placeholder="Option Value (e.g., Red, XL, 1 KG)" 
                                                class="flex-1 rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-brand-orange focus:outline-none focus:ring-1 focus:ring-brand-orange">
                                            <button type="button" @click="removeOption(index, oIndex)" class="text-gray-400 hover:text-red-500 p-1" x-show="variant.options.length > 1">
                                                <i class="fa-solid fa-xmark"></i>
                                            </button>
                                        </div>
                                    </template>
                                </div>

                                <!-- Price / SKU / Stock row -->
                                <div class="grid gap-3 sm:grid-cols-4">
                                    <div>
                                        <label class="block text-xs font-medium text-gray-600 mb-1">Price <span class="text-red-500">*</span></label>
                                        <div class="relative">
                                            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs">৳</span>
                                            <input type="number" step="0.01" :name="`variants[${index}][current_price]`" x-model="variant.current_price" placeholder="0.00"
                                                class="block w-full pl-6 pr-3 rounded-md border border-gray-300 py-2 text-sm focus:border-brand-orange focus:outline-none">
                                        </div>
                                    </div>
                                    <div>
                                        <label class="block text-xs font-medium text-gray-600 mb-1">Old Price</label>
                                        <div class="relative">
                                            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs">৳</span>
                                            <input type="number" step="0.01" :name="`variants[${index}][original_price]`" x-model="variant.original_price" placeholder="0.00"
                                                class="block w-full pl-6 pr-3 rounded-md border border-gray-300 py-2 text-sm focus:border-brand-orange focus:outline-none">
                                        </div>
                                    </div>
                                    <div>
                                        <label class="block text-xs font-medium text-gray-600 mb-1">SKU <span class="text-gray-400 font-normal">(Auto if blank)</span></label>
                                        <input type="text" :name="`variants[${index}][sku]`" x-model="variant.sku" placeholder="e.g. 261010191234"
                                            class="block w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-brand-orange focus:outline-none">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-medium text-gray-600 mb-1">Stock Quantity <span class="text-red-500">*</span></label>
                                        <input type="number" :name="`variants[${index}][stock]`" x-model="variant.stock" placeholder="0"
                                            class="block w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-brand-orange focus:outline-none">
                                    </div>
                                </div>

                                <!-- Variant Image -->
                                <div x-data="variantImageUploader(index)" class="border border-dashed border-gray-300 rounded-lg p-3 bg-gray-50">
                                    <p class="text-xs font-medium text-gray-600 mb-2">
                                        <i class="fa-solid fa-image mr-1 text-gray-400"></i>
                                        Variant Image <span class="text-gray-400 font-normal">(Used as selector on frontend)</span>
                                    </p>
                                    <div class="flex items-center gap-3">
                                        <div class="relative w-16 h-16 rounded-lg border border-gray-200 overflow-hidden bg-white shrink-0 flex items-center justify-center cursor-pointer hover:border-brand-orange transition-colors" @click="$refs.variantFileInput.click()">
                                            <template x-if="previewUrl">
                                                <img :src="previewUrl" class="w-full h-full object-cover">
                                            </template>
                                            <template x-if="!previewUrl && variant.existing_image">
                                                <img :src="variant.existing_image" class="w-full h-full object-cover">
                                            </template>
                                            <template x-if="!previewUrl && !variant.existing_image">
                                                <i class="fa-regular fa-image text-gray-300 text-xl"></i>
                                            </template>
                                        </div>
                                        <div class="flex-1">
                                            <button type="button" @click="$refs.variantFileInput.click()" class="text-xs font-medium text-brand-orange hover:underline">
                                                <i class="fa-solid fa-upload mr-1"></i>
                                                Upload a swatch or product image.
                                            </button>
                                            <p class="text-xs text-gray-400 mt-0.5">Max size: 2MB.</p>
                                            <input type="file" :name="`variant_images[${index}]`" accept="image/*" class="hidden" x-ref="variantFileInput"
                                                @change="handleFileChange($event)">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </template>

                    <button type="button" @click="addVariant" class="w-full py-3 border-2 border-dashed border-gray-300 rounded-lg text-sm font-medium text-brand-orange hover:bg-gray-50 hover:border-brand-orange transition-colors flex items-center justify-center gap-2">
                        <i class="fa-solid fa-circle-plus"></i> Add New Variant
                    </button>
                </div>
                
                <div x-show="!hasVariants" class="text-center py-8 px-4 rounded-md border-2 border-dashed border-gray-200 bg-gray-50">
                    <p class="text-sm text-gray-500">This product is a single variant product.</p>
                    <p class="text-xs text-gray-400 mt-1">Price and inventory are managed in the Pricing tab.</p>
                </div>
            </div>
        </div>

        <!-- Step 4: Product Media -->
        <div x-show="step === 4" style="display: none;" x-cloak>
            <h3 class="text-base font-medium text-gray-900 mb-5">Media</h3>
            
            <div class="grid gap-6 sm:grid-cols-3">
                <div class="sm:col-span-1">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Primary Image <span class="text-red-500">*</span></label>
                    <div x-data="mainImageUploader()" class="relative aspect-square rounded-md border border-dashed border-gray-300 overflow-hidden bg-gray-50 flex items-center justify-center hover:bg-gray-100 transition-colors cursor-pointer">
                        <template x-if="!imageUrl && !hasExistingImage">
                            <div class="text-center p-4">
                                <i class="fa-regular fa-image text-2xl text-gray-400 mb-2"></i>
                                <span class="block text-xs text-gray-500">Upload Image</span>
                            </div>
                        </template>
                        <template x-if="imageUrl">
                            <img :src="imageUrl" class="w-full h-full object-cover">
                        </template>
                        <template x-if="!imageUrl && hasExistingImage">
                            <img src="{{ isset($product) && $product->main_image ? image_url($product->main_image) : '' }}" class="w-full h-full object-cover">
                        </template>
                        <input type="file" name="main_image" accept="image/*" data-required="{{ isset($product) && $product->main_image ? '0' : '1' }}" class="absolute inset-0 opacity-0 cursor-pointer" @change="handleFileChange($event); delete errors.main_image">
                    </div>
                    <p x-show="errors.main_image" x-text="errors.main_image" class="mt-1 text-xs text-red-600" style="display:none;"></p>
                </div>

                <div class="sm:col-span-2" x-data="multiImageUploader()">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Media Gallery</label>
                    <div class="flex justify-center items-center rounded-md border border-dashed border-gray-300 px-6 py-10 transition-colors bg-gray-50"
                         :class="{ 'bg-gray-100 border-gray-400': isDragging }"
                         @dragover.prevent="isDragging = true"
                         @dragleave.prevent="isDragging = false"
                         @drop.prevent="handleDrop($event)">
                         
                        <div class="text-center">
                            <i class="fa-solid fa-cloud-arrow-up text-3xl text-gray-400 mb-2"></i>
                            <div class="flex text-sm text-gray-600 justify-center">
                                <label for="images-upload" class="relative cursor-pointer rounded-md font-medium text-brand-orange hover:text-brand-orange/80 focus-within:outline-none">
                                    <span>Upload files</span>
                                    <input id="images-upload" name="images[]" type="file" multiple accept="image/*,video/*" class="sr-only" @change="handleFileSelect">
                                </label>
                                <p class="pl-1">or drag and drop</p>
                            </div>
                            <p class="text-xs text-gray-500 mt-1">Images (PNG, JPG, WEBP) &amp; videos (MP4, MOV, WEBM) up to 20MB</p>
                        </div>
                    </div>

                    <!-- Previews of newly selected files -->
                    <template x-if="newFiles.length > 0">
                        <div class="mt-4">
                            <p class="text-xs font-medium text-gray-500 mb-2">Selected for upload</p>
                            <div class="flex flex-wrap gap-3">
                                <template x-for="(fileItem, index) in newFiles" :key="index">
                                    <div class="relative group">
                                        <template x-if="fileItem.type === 'image'">
                                            <img :src="fileItem.url" class="h-16 w-16 rounded border border-gray-200 object-cover">
                                        </template>
                                        <template x-if="fileItem.type === 'video'">
                                            <div class="h-16 w-16 rounded border border-gray-200 bg-gray-100 flex items-center justify-center">
                                                <i class="fa-solid fa-video text-gray-400"></i>
                                            </div>
                                        </template>
                                        <button type="button" @click="removeNewFile(index)" class="absolute -right-1.5 -top-1.5 rounded-full bg-white border border-gray-200 w-5 h-5 text-gray-600 hover:text-red-500 flex items-center justify-center shadow-sm">
                                            <i class="fa-solid fa-xmark" style="font-size: 10px;"></i>
                                        </button>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </template>
                    
                    <!-- Existing Gallery -->
                    @if (! empty($product) && $product->images->count())
                        <div class="mt-6 border-t border-gray-100 pt-4">
                            <p class="text-xs font-medium text-gray-500 mb-2">Current gallery</p>
                            <div class="flex flex-wrap gap-3">
                                @foreach ($product->images as $image)
                                    <div class="relative group">
                                        @if(is_video($image->image))
                                            <video src="{{ image_url($image->image) }}" class="h-16 w-16 rounded border border-gray-200 object-cover" muted></video>
                                        @else
                                            <img src="{{ image_url($image->image) }}" class="h-16 w-16 rounded border border-gray-200 object-cover">
                                        @endif
                                        <button type="button" @click="if(confirm('Delete this image?')) { let f = document.createElement('form'); f.method = 'POST'; f.action = '{{ route('admin.products.images.destroy', [$product, $image]) }}'; f.innerHTML = '<input type=\'hidden\' name=\'_method\' value=\'DELETE\'><input type=\'hidden\' name=\'_token\' value=\'{{ csrf_token() }}\'>'; document.body.appendChild(f); f.submit(); }" class="absolute -right-1.5 -top-1.5 rounded-full bg-white border border-gray-200 w-5 h-5 text-gray-600 hover:text-red-500 flex items-center justify-center shadow-sm">
                                            <i class="fa-solid fa-trash-can" style="font-size: 10px;"></i>
                                        </button>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Step 5: Visibility & Settings -->
        <div x-show="step === 5" style="display: none;" x-cloak>
            <h3 class="text-base font-medium text-gray-900 mb-5">Visibility & Settings</h3>
            
            <div class="space-y-4">
                <div class="flex items-start">
                    <div class="flex h-5 items-center">
                        <input type="checkbox" name="is_active" id="is_active" value="1" @checked(old('is_active', $product->is_active ?? true)) class="h-4 w-4 rounded border-gray-300 text-brand-orange focus:ring-brand-orange">
                    </div>
                    <div class="ml-3 text-sm">
                        <label for="is_active" class="font-medium text-gray-700">Active (Visible)</label>
                        <p class="text-gray-500 text-xs mt-0.5">Product will be visible on the store and available for purchase.</p>
                    </div>
                </div>

                <div class="flex items-start pt-3 border-t border-gray-100">
                    <div class="flex h-5 items-center">
                        <input type="checkbox" name="is_featured" id="is_featured" value="1" @checked(old('is_featured', $product->is_featured ?? false)) class="h-4 w-4 rounded border-gray-300 text-brand-orange focus:ring-brand-orange">
                    </div>
                    <div class="ml-3 text-sm">
                        <label for="is_featured" class="font-medium text-gray-700">Featured Product</label>
                        <p class="text-gray-500 text-xs mt-0.5">Show this product in the featured section on the homepage.</p>
                    </div>
                </div>

                <div class="flex items-start pt-3 border-t border-gray-100">
                    <div class="flex h-5 items-center">
                        <input type="checkbox" name="is_best_seller" id="is_best_seller" value="1" @checked(old('is_best_seller', $product->is_best_seller ?? false)) class="h-4 w-4 rounded border-gray-300 text-brand-orange focus:ring-brand-orange">
                    </div>
                    <div class="ml-3 text-sm">
                        <label for="is_best_seller" class="font-medium text-gray-700">Best Seller Label</label>
                        <p class="text-gray-500 text-xs mt-0.5">Display a Best Seller badge on the product card.</p>
                    </div>
                </div>

                <div class="flex items-start pt-3 border-t border-gray-100">
                    <div class="flex h-5 items-center">
                        <input type="checkbox" name="is_new_arrival" id="is_new_arrival" value="1" @checked(old('is_new_arrival', $product->is_new_arrival ?? false)) class="h-4 w-4 rounded border-gray-300 text-brand-orange focus:ring-brand-orange">
                    </div>
                    <div class="ml-3 text-sm">
                        <label for="is_new_arrival" class="font-medium text-gray-700">New Arrival Label</label>
                        <p class="text-gray-500 text-xs mt-0.5">Display a New Arrival badge on the product card.</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Step 6: Sales & Ratings Metrics Strategy -->
        <div x-show="step === 6" style="display: none;" x-cloak>
            <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 border-b border-gray-100 pb-4">
                <div>
                    <h3 class="text-base font-bold text-gray-900">Sales & Ratings Metrics Strategy</h3>
                    <p class="text-xs text-gray-500 mt-0.5">Simulate realistic, organic-looking sales and rating growth over time without suspicious jumps or reload changes.</p>
                </div>
                <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-50 px-3 py-1 text-xs font-semibold text-emerald-700 w-fit">
                    <i class="fa-solid fa-shield-halved text-[11px]"></i> Refresh-Safe Natural Growth
                </span>
            </div>

            <!-- Current Calculated Live Status (If editing product) -->
            @if(!empty($product) && $product->id)
                <div class="mb-6 rounded-xl border border-blue-100 bg-blue-50/50 p-4">
                    <div class="flex items-center justify-between flex-wrap gap-2">
                        <div class="flex items-center gap-2">
                            <i class="fa-solid fa-circle-check text-blue-600 text-sm"></i>
                            <span class="text-xs font-bold text-blue-900 uppercase tracking-wide">Live Result on Details Page Today:</span>
                        </div>
                        <div class="flex items-center gap-3 text-xs font-semibold text-gray-700">
                            <span class="bg-white px-2.5 py-1 rounded-md border border-blue-200 shadow-2xs">
                                Sold: <strong class="text-emerald-600">{{ $product->display_sold_count }} units</strong>
                            </span>
                            <span class="bg-white px-2.5 py-1 rounded-md border border-blue-200 shadow-2xs flex items-center gap-1">
                                Rating: <i class="fa-solid fa-star text-amber-400 text-[11px]"></i>
                                <strong class="text-gray-900">{{ number_format($product->display_rating, 1) }} / 5</strong>
                                <span class="text-gray-500">({{ $product->display_reviews_count }} Ratings)</span>
                            </span>
                        </div>
                    </div>
                </div>
            @endif

            <div class="space-y-6">
                <!-- 1. Sales & Sold Count Strategy Card -->
                <div class="rounded-xl border border-gray-200 bg-white p-5 sm:p-6 shadow-2xs space-y-5">
                    <div class="flex items-center gap-2.5 pb-3 border-b border-gray-100">
                        <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-emerald-50 text-emerald-600">
                            <i class="fa-solid fa-chart-line text-sm"></i>
                        </span>
                        <div>
                            <h4 class="font-bold text-gray-900 text-sm">Sales & Sold Count Strategy</h4>
                            <p class="text-xs text-gray-500">Configure how sold units are calculated and displayed.</p>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1.5">Metrics Mode</label>
                        <select name="sales_metrics_mode" x-model="salesMetricsMode" class="block w-full rounded-lg border border-gray-300 px-3.5 py-2.5 text-xs sm:text-sm text-gray-800 bg-white focus:border-brand-orange focus:ring-1 focus:ring-brand-orange">
                            <option value="organic">Organic (Show actual order volume)</option>
                            <option value="optimized">Optimized (Show calculated growth trend over time)</option>
                        </select>
                        <p class="text-[11px] text-gray-500 mt-1">
                            <span x-show="salesMetricsMode === 'organic'">Organic shows actual customer order volume.</span>
                            <span x-show="salesMetricsMode === 'optimized'">Optimized simulates steady sold count growth based on parameters below. Refreshes will NOT jump randomly.</span>
                        </p>
                    </div>

                    <div x-show="salesMetricsMode === 'optimized'" class="grid gap-4 sm:grid-cols-3 pt-2">
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 mb-1">Base / Initial Sold Count</label>
                            <input type="number" name="base_sold_count" min="0" value="{{ old('base_sold_count', $product->base_sold_count ?? 15) }}"
                                class="w-full rounded-lg border border-gray-300 px-3 py-2 text-xs sm:text-sm text-gray-800 focus:border-brand-orange focus:ring-1 focus:ring-brand-orange">
                            <p class="text-[11px] text-gray-400 mt-1">Starting baseline units (e.g. 15)</p>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-gray-700 mb-1">Trend Start Date</label>
                            <input type="date" name="sales_start_date" value="{{ old('sales_start_date', !empty($product->sales_start_date) ? \Carbon\Carbon::parse($product->sales_start_date)->format('Y-m-d') : now()->subDays(7)->format('Y-m-d')) }}"
                                class="w-full rounded-lg border border-gray-300 px-3 py-2 text-xs sm:text-sm text-gray-800 focus:border-brand-orange focus:ring-1 focus:ring-brand-orange">
                            <p class="text-[11px] text-gray-400 mt-1">Start date of growth calculation</p>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-gray-700 mb-1">Estimated Daily Sales Volume</label>
                            <input type="number" name="daily_sales_volume" min="0" value="{{ old('daily_sales_volume', $product->daily_sales_volume ?? 2) }}"
                                class="w-full rounded-lg border border-gray-300 px-3 py-2 text-xs sm:text-sm text-gray-800 focus:border-brand-orange focus:ring-1 focus:ring-brand-orange">
                            <p class="text-[11px] text-gray-400 mt-1">Average units per day (e.g. 2)</p>
                        </div>
                    </div>
                </div>

                <!-- 2. Ratings & Review Strategy Card -->
                <div class="rounded-xl border border-gray-200 bg-white p-5 sm:p-6 shadow-2xs space-y-5">
                    <div class="flex items-center gap-2.5 pb-3 border-b border-gray-100">
                        <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-amber-50 text-amber-500">
                            <i class="fa-solid fa-star text-sm"></i>
                        </span>
                        <div>
                            <h4 class="font-bold text-gray-900 text-sm">Ratings & Review Strategy</h4>
                            <p class="text-xs text-gray-500">Configure auto-generated rating score and review volume over time.</p>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1.5">Rating Mode</label>
                        <select name="rating_metrics_mode" x-model="ratingMetricsMode" class="block w-full rounded-lg border border-gray-300 px-3.5 py-2.5 text-xs sm:text-sm text-gray-800 bg-white focus:border-brand-orange focus:ring-1 focus:ring-brand-orange">
                            <option value="organic">Organic (Show actual customer ratings)</option>
                            <option value="optimized">Optimized (Auto generate ratings over time)</option>
                        </select>
                        <p class="text-[11px] text-gray-500 mt-1">
                            <span x-show="ratingMetricsMode === 'organic'">Only verified customer reviews and stars will be shown.</span>
                            <span x-show="ratingMetricsMode === 'optimized'">Simulates realistic ratings and reviews growing over calendar intervals.</span>
                        </p>
                    </div>

                    <div x-show="ratingMetricsMode === 'optimized'" class="space-y-4 pt-2">
                        <div class="grid gap-4 sm:grid-cols-3">
                            <div>
                                <label class="block text-xs font-semibold text-gray-700 mb-1">Base / Initial Ratings Count</label>
                                <input type="number" name="base_reviews_count" min="0" value="{{ old('base_reviews_count', $product->base_reviews_count ?? 3) }}"
                                    class="w-full rounded-lg border border-gray-300 px-3 py-2 text-xs sm:text-sm text-gray-800 focus:border-brand-orange focus:ring-1 focus:ring-brand-orange">
                                <p class="text-[11px] text-gray-400 mt-1">Starting reviews (e.g. 3)</p>
                            </div>

                            <div>
                                <label class="block text-xs font-semibold text-gray-700 mb-1">Start Date</label>
                                <input type="date" name="rating_start_date" value="{{ old('rating_start_date', !empty($product->rating_start_date) ? \Carbon\Carbon::parse($product->rating_start_date)->format('Y-m-d') : now()->subDays(10)->format('Y-m-d')) }}"
                                    class="w-full rounded-lg border border-gray-300 px-3 py-2 text-xs sm:text-sm text-gray-800 focus:border-brand-orange focus:ring-1 focus:ring-brand-orange">
                                <p class="text-[11px] text-gray-400 mt-1">Baseline start date</p>
                            </div>

                            <div>
                                <label class="block text-xs font-semibold text-gray-700 mb-1">Interval (Days)</label>
                                <input type="number" name="rating_interval_days" min="1" value="{{ old('rating_interval_days', $product->rating_interval_days ?? 4) }}"
                                    class="w-full rounded-lg border border-gray-300 px-3 py-2 text-xs sm:text-sm text-gray-800 focus:border-brand-orange focus:ring-1 focus:ring-brand-orange">
                                <p class="text-[11px] text-gray-400 mt-1">Days between new reviews (e.g. 4)</p>
                            </div>
                        </div>

                        <div class="grid gap-4 sm:grid-cols-3">
                            <div>
                                <label class="block text-xs font-semibold text-gray-700 mb-1">Ratings per Interval</label>
                                <input type="number" name="ratings_per_interval" min="0" value="{{ old('ratings_per_interval', $product->ratings_per_interval ?? 1) }}"
                                    class="w-full rounded-lg border border-gray-300 px-3 py-2 text-xs sm:text-sm text-gray-800 focus:border-brand-orange focus:ring-1 focus:ring-brand-orange">
                                <p class="text-[11px] text-gray-400 mt-1">Ratings added per interval</p>
                            </div>

                            <div>
                                <label class="block text-xs font-semibold text-gray-700 mb-1">Min Rating</label>
                                <input type="number" step="0.05" min="1" max="5" name="min_rating" value="{{ old('min_rating', $product->min_rating ?? 4.50) }}"
                                    class="w-full rounded-lg border border-gray-300 px-3 py-2 text-xs sm:text-sm text-gray-800 focus:border-brand-orange focus:ring-1 focus:ring-brand-orange">
                                <p class="text-[11px] text-gray-400 mt-1">Lowest rating (e.g. 4.50)</p>
                            </div>

                            <div>
                                <label class="block text-xs font-semibold text-gray-700 mb-1">Max Rating</label>
                                <input type="number" step="0.05" min="1" max="5" name="max_rating" value="{{ old('max_rating', $product->max_rating ?? 5.00) }}"
                                    class="w-full rounded-lg border border-gray-300 px-3 py-2 text-xs sm:text-sm text-gray-800 focus:border-brand-orange focus:ring-1 focus:ring-brand-orange">
                                <p class="text-[11px] text-gray-400 mt-1">Highest rating (e.g. 5.00)</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 3. SEO Metadata Card (Meta Title & Meta Description) -->
                <div class="rounded-xl border border-gray-200 bg-white p-5 sm:p-6 shadow-2xs space-y-4">
                    <div class="flex items-center gap-2.5 pb-3 border-b border-gray-100">
                        <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-blue-50 text-blue-600">
                            <i class="fa-brands fa-google text-sm"></i>
                        </span>
                        <div>
                            <h4 class="font-bold text-gray-900 text-sm">Search Engine Optimization (SEO)</h4>
                            <p class="text-xs text-gray-500">Custom Meta Title & Description for Google search results.</p>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1">Meta Title</label>
                        <input type="text" name="meta_title" value="{{ old('meta_title', $product->meta_title ?? '') }}"
                            placeholder="e.g. {{ !empty($product->name) ? $product->name . ' | Raimart' : 'Product Name | Raimart' }}"
                            class="w-full rounded-lg border border-gray-300 px-3 py-2 text-xs sm:text-sm text-gray-800 focus:border-brand-orange focus:ring-1 focus:ring-brand-orange">
                        <p class="text-[11px] text-gray-400 mt-1">Leave empty to use <code>{Product Name} - Raimart</code> automatically.</p>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1">Meta Description</label>
                        <textarea name="meta_description" rows="2"
                            placeholder="Brief search summary displayed in Google search results..."
                            class="w-full rounded-lg border border-gray-300 p-3 text-xs sm:text-sm text-gray-800 focus:border-brand-orange focus:ring-1 focus:ring-brand-orange resize-none">{{ old('meta_description', $product->meta_description ?? '') }}</textarea>
                    </div>
                </div>
            </div>
        </div>

        <!-- Navigation Actions -->
        <div class="sticky bottom-0 z-40 mt-8 flex items-center justify-end border-t border-gray-200 bg-white py-4 w-full gap-3">
            <button type="button" @click="step--" x-show="step > 1" class="mr-auto rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-brand-orange focus:ring-offset-2 shadow-sm" style="display: none;">
                Previous
            </button>

            <button type="submit"
                @click="if (! validateAll()) { $event.preventDefault(); } else if (window.quill) { document.getElementById('description-input').value = quill.root.innerHTML; }"
                class="rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-brand-orange focus:ring-offset-2">
                Save
            </button>

            <button type="button" @click="if (validateStep(step)) step++" x-show="step < maxStep" class="rounded-md border border-transparent bg-brand-navy px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-brand-navy/90 focus:outline-none focus:ring-2 focus:ring-brand-navy focus:ring-offset-2">
                Next
            </button>
        </div>
    </div>
</div>

<!-- Initialize Scripts -->
<script>
    // Initialize Quill Editor
    var quill;
    document.addEventListener("livewire:navigated", function() {
        if(typeof Quill !== 'undefined' && document.getElementById('quill-editor')) {
            quill = new Quill('#quill-editor', {
                theme: 'snow',
                placeholder: 'Write a compelling product description...',
                modules: {
                    toolbar: [
                        [{ 'header': [1, 2, 3, false] }],
                        ['bold', 'italic', 'underline', 'strike'],
                        [{ 'list': 'ordered'}, { 'list': 'bullet' }],
                        [{ 'color': [] }, { 'background': [] }],
                        ['link', 'clean']
                    ]
                }
            });
        }
    });

    // Multi-step wizard state + step-by-step client-side validation.
    // Defined as a plain global (not Alpine.data) so it works even though the
    // admin loads Alpine twice (CDN + Livewire).
    window.productWizard = function () {
        return {
            step: 1,
            maxStep: 6,
            salesMetricsMode: '{{ old('sales_metrics_mode', $product->sales_metrics_mode ?? 'organic') }}',
            ratingMetricsMode: '{{ old('rating_metrics_mode', $product->rating_metrics_mode ?? 'organic') }}',
            errors: {},

            // Required fields per step (main_image only when marked required).
            stepFields: {
                1: [
                    { name: 'name', msg: 'Product name is required.' },
                    { name: 'category_id', msg: 'Please select a category.' },
                ],
                2: [
                    { name: 'price', msg: 'Price is required.' },
                    { name: 'stock', msg: 'Stock quantity is required.' },
                ],
                4: [
                    { name: 'main_image', file: true, msg: 'Primary image is required.' },
                ],
            },

            check(fields, errs) {
                let ok = true;
                (fields || []).forEach((f) => {
                    const el = this.$root.querySelector('[name="' + f.name + '"]');
                    let bad;
                    if (f.file) {
                        bad = !!el && el.dataset.required === '1' && el.files.length === 0;
                    } else {
                        bad = !el || el.value.trim() === '';
                    }
                    if (bad) { errs[f.name] = f.msg; ok = false; } else { delete errs[f.name]; }
                });
                return ok;
            },

            // Validate the current step (Next button).
            validateStep(n) {
                const errs = Object.assign({}, this.errors);
                const ok = this.check(this.stepFields[n], errs);
                this.errors = errs;
                return ok;
            },

            // Validate every step; jump to the first one with an error (Save button).
            validateAll() {
                const errs = {};
                let firstBad = null;
                [1, 2, 3, 4, 5, 6].forEach((n) => {
                    if (!this.check(this.stepFields[n], errs) && firstBad === null) {
                        firstBad = n;
                    }
                });
                this.errors = errs;
                if (firstBad !== null) this.step = firstBad;
                return firstBad === null;
            },
        };
    };

    document.addEventListener('alpine:init', () => {
        Alpine.data('skuChecker', (initialSku, ignoreId) => ({
            sku: initialSku,
            ignoreId: ignoreId,
            isChecking: false,
            isValid: null,
            message: '',
            
            get inputClass() {
                if (this.isChecking) return 'border-gray-300 focus:border-brand-orange focus:ring-brand-orange';
                if (this.isValid === false) return 'border-red-500 focus:border-red-500 focus:ring-red-500';
                if (this.isValid === true && this.sku.length > 0) return 'border-green-500 focus:border-green-500 focus:ring-green-500';
                return 'border-gray-300 focus:border-brand-orange focus:ring-brand-orange';
            },

            async checkSku() {
                if (!this.sku || this.sku.trim() === '') {
                    this.isValid = null;
                    this.message = '';
                    return;
                }
                
                this.isChecking = true;
                this.message = '';
                
                try {
                    const url = new URL('{{ route("admin.products.check-sku") }}');
                    url.searchParams.append('sku', this.sku);
                    if (this.ignoreId) {
                        url.searchParams.append('ignore_id', this.ignoreId);
                    }
                    
                    const response = await fetch(url);
                    const data = await response.json();
                    
                    this.isValid = data.valid;
                    this.message = data.message;
                } catch (e) {
                    this.isValid = null;
                    this.message = 'Error checking SKU';
                } finally {
                    this.isChecking = false;
                }
            }
        }));

        Alpine.data('variantBuilder', (initialVariants, initialHasVariants, initialShowVariantImage) => ({
            hasVariants: initialHasVariants || false,
            showVariantImage: initialShowVariantImage || false,
            optimizeWebp: false,
            variants: initialVariants.length ? initialVariants.map(v => ({
                ...v,
                options: v.attributes ? Object.entries(v.attributes).map(([name, value]) => ({name, value})) : [{name: '', value: ''}],
                existing_image: v.images && v.images[0] ? v.images[0] : null
            })) : [
                {
                    id: null,
                    options: [{name: '', value: ''}],
                    current_price: '',
                    original_price: '',
                    sku: '',
                    stock: 0,
                    existing_image: null
                }
            ],
            addVariant() {
                this.variants.push({
                    id: null,
                    options: [{name: '', value: ''}],
                    current_price: '',
                    original_price: '',
                    sku: '',
                    stock: 0,
                    existing_image: null
                });
            },
            removeVariant(index) {
                this.variants.splice(index, 1);
            },
            addOption(variantIndex) {
                this.variants[variantIndex].options.push({name: '', value: ''});
            },
            removeOption(variantIndex, optionIndex) {
                this.variants[variantIndex].options.splice(optionIndex, 1);
            }
        }));

        Alpine.data('variantImageUploader', (variantIndex) => ({
            previewUrl: null,
            handleFileChange(event) {
                const file = event.target.files[0];
                if (file) {
                    this.previewUrl = URL.createObjectURL(file);
                }
            }
        }));

        Alpine.data('mainImageUploader', () => ({
            imageUrl: null,
            hasExistingImage: {{ isset($product) && $product->main_image ? 'true' : 'false' }},
            handleFileChange(event) {
                const file = event.target.files[0];
                if (file) {
                    this.imageUrl = URL.createObjectURL(file);
                }
            }
        }));

        Alpine.data('multiImageUploader', () => ({
            isDragging: false,
            newFiles: [],
            
            handleDrop(event) {
                this.isDragging = false;
                const files = event.dataTransfer.files;
                if (files.length > 0) {
                    this.appendFiles(files);
                }
            },
            
            handleFileSelect(event) {
                const files = event.target.files;
                if (files.length > 0) {
                    this.appendFiles(files);
                }
            },
            
            appendFiles(files) {
                const dataTransfer = new DataTransfer();
                const fileInput = document.getElementById('images-upload');
                
                // Add existing tracked files to data transfer
                this.newFiles.forEach(item => {
                    dataTransfer.items.add(item.file);
                });
                
                // Add new files
                Array.from(files).forEach(file => {
                    if (file.type.startsWith('image/') || file.type.startsWith('video/')) {
                        // Prevent duplicates by checking if file already exists in newFiles
                        const exists = this.newFiles.find(f => f.file.name === file.name && f.file.size === file.size);
                        
                        if (!exists) {
                            dataTransfer.items.add(file);
                            const isImage = file.type.startsWith('image/');
                            if (isImage) {
                                const reader = new FileReader();
                                reader.onload = (e) => {
                                    this.newFiles.push({
                                        file: file,
                                        url: e.target.result,
                                        type: 'image'
                                    });
                                };
                                reader.readAsDataURL(file);
                            } else {
                                this.newFiles.push({
                                    file: file,
                                    url: null,
                                    type: 'video'
                                });
                            }
                        }
                    }
                });
                
                fileInput.files = dataTransfer.files;
            },
            
            removeNewFile(index) {
                this.newFiles.splice(index, 1);
                
                // Rebuild FileList for the input
                const dataTransfer = new DataTransfer();
                const fileInput = document.getElementById('images-upload');
                
                this.newFiles.forEach(item => {
                    dataTransfer.items.add(item.file);
                });
                
                fileInput.files = dataTransfer.files;
            }
        }));
    });
</script>
