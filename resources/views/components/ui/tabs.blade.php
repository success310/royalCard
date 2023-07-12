@if ($tabs != null)
    <div class="{{ $tabClass }}" x-data="{ 
            activeTab: 'tab-{{ $activeTab }}',
            updateTabIndex(tab) {
                this.$refs.tabIndex.value = tab.replace('tab-', '');
            }
        }">
        <ul class="{{ $style }}">
            @foreach ($tabs as $tab)
                <li x-on:click="activeTab = 'tab-{{ $loop->iteration }}'; updateTabIndex(activeTab); $dispatch('onclicktab', { tab: 'tab-{{ $loop->iteration }}' })">
                    <a href="javascript:void(0);" class="focus:z-{{ (count($tabs) + 1) * 10 - ($loop->iteration) * 10 }}"
                        x-bind:class="activeTab === 'tab-{{ $loop->iteration }}' ? 'active' : ''">{{ $tab }}</a>
                </li>
            @endforeach
        </ul>
        @foreach ($tabs as $tab)
            <div x-show="activeTab === 'tab-{{ $loop->iteration }}'" {{ $attributes ?? '' }}>
                {{ ${'tab' . ($loop->iteration)} }}
            </div>
        @endforeach
        <input type="hidden" id="current_tab_index" name="current_tab_index" x-ref="tabIndex" value="{{ $activeTab }}">
    </div>
@endif
