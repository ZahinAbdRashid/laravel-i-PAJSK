@extends('layouts.app')

@section('title', 'Activity Suggestion Rules')

@push('styles')
<style>
    .rule-card { border-left: 3px solid #e2e8f0; transition: all 0.2s ease; }
    .rule-card:hover { border-left-color: #162660; background-color: #f8fafc; }
    .condition-chip { background-color: #f1f5f9; border: 1px solid #e2e8f0; font-size: 11px; letter-spacing: 0.02em; }
    .action-item { background-color: #ffffff; border: 1px solid #f1f5f9; border-radius: 6px; transition: all 0.2s ease; }
    .action-item:hover { border-color: #e2e8f0; }
    .status-active { background-color: #dcfce7; color: #166534; font-size: 11px; padding: 2px 8px; border-radius: 10px; }
    .status-inactive { background-color: #f3f4f6; color: #6b7280; font-size: 11px; padding: 2px 8px; border-radius: 10px; }
    .priority-badge { background-color: #f8fafc; color: #64748b; border: 1px solid #e2e8f0; font-size: 11px; padding: 2px 8px; border-radius: 10px; }
</style>
@endpush

@section('content')
<!-- Page Header -->
<div class="mb-8">
    <div class="flex items-center gap-3 mb-3">
        <div class="p-2 bg-indigo-50 rounded-lg">
            <i class="fas fa-code-branch text-lg text-indigo-900"></i>
        </div>
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Activity Suggestion Rules</h1>
            <p class="text-gray-600">Define rules to generate personalized activity suggestions for students</p>
        </div>
    </div>
</div>

<!-- Rules Management Section -->
<div class="grid grid-cols-1 gap-8">
    <!-- Rules List -->
    <div>
        <div class="bg-white rounded-lg border border-gray-200 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900">Rules List</h3>
                        <p class="text-sm text-gray-600 mt-1">Rules are evaluated in priority order</p>
                    </div>
                    <div class="flex gap-2">
                        <button onclick="showAddRuleModal()" class="px-4 py-2.5 bg-indigo-900 text-white rounded-lg hover:bg-indigo-800 transition flex items-center gap-2 text-sm font-medium">
                            <i class="fas fa-plus"></i>
                            Add New Rule
                        </button>
                    </div>
                </div>
            </div>
            <div class="p-6">
                @if(session('success'))
                <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                    {{ session('success') }}
                </div>
                @endif

                <!-- Empty State -->
                <div id="noRulesMessage" class="text-center py-12 {{ $rules->count() > 0 ? 'hidden' : '' }}">
                    <div class="w-20 h-20 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-4 border border-gray-200">
                        <i class="fas fa-code-branch text-3xl text-gray-400"></i>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-800 mb-2">No Rules Defined</h3>
                    <p class="text-gray-600 mb-6 max-w-md mx-auto">Create your first rule to start generating personalized suggestions for students</p>
                </div>

                <!-- Rules List -->
                <div id="rulesContainer" class="space-y-4">
                    @forelse($rules as $rule)
                    <div class="rule-card bg-white border border-gray-200 rounded-lg p-5">
                        <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-4 mb-4">
                            <div class="flex-1">
                                <div class="flex items-center gap-2 mb-2">
                                    <h4 class="font-semibold text-gray-900">{{ $rule->name }}</h4>
                                    @if($rule->active)
                                        <span class="status-active">Active</span>
                                    @else
                                        <span class="status-inactive">Inactive</span>
                                    @endif
                                    <span class="priority-badge">Priority: {{ $rule->priority }}</span>
                                </div>
                                
                                <!-- Conditions -->
                                <div class="mb-4">
                                    <div class="text-xs font-medium text-gray-500 mb-2 uppercase tracking-wider">Conditions</div>
                                    <div class="flex flex-wrap gap-1.5">
                                        @foreach($rule->conditions as $condition)
                                        <span class="condition-chip px-2.5 py-1 rounded">
                                            @php
                                                $typeLabels = [
                                                    'grade' => 'Grade',
                                                    'score' => 'Score',
                                                    'weak_component' => 'Weak in',
                                                    'has_activity' => 'Has',
                                                    'missing_activity' => 'Missing'
                                                ];
                                                $operatorSymbols = [
                                                    'equals' => '=',
                                                    'greater_than' => '>',
                                                    'less_than' => '<',
                                                    'greater_or_equal' => '≥',
                                                    'less_or_equal' => '≤',
                                                    'not_equals' => '≠'
                                                ];
                                                $typeLabel = $typeLabels[$condition['type']] ?? $condition['type'];
                                                $operatorSymbol = $operatorSymbols[$condition['operator']] ?? $condition['operator'];
                                            @endphp
                                            {{ $typeLabel }} {{ $operatorSymbol }} {{ $condition['value'] }}
                                        </span>
                                        @endforeach
                                    </div>
                                </div>
                                
                                <!-- Actions -->
                                <div>
                                    <div class="text-xs font-medium text-gray-500 mb-2 uppercase tracking-wider">Suggestions</div>
                                    <div class="space-y-2">
                                        @foreach($rule->actions as $action)
                                        <div class="action-item p-3">
                                            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 mb-1">
                                                <div class="font-medium text-gray-900">{{ $action['activityName'] }}</div>
                                                <div class="flex items-center gap-3 text-xs text-gray-500">
                                                    <span>{{ $action['level'] }}</span>
                                                    <span>•</span>
                                                    <span>{{ $action['achievement'] }}</span>
                                                    <span>•</span>
                                                    <span class="font-medium">+{{ $action['points'] }} pts</span>
                                                </div>
                                            </div>
                                            <div class="text-sm text-gray-600">{{ $action['message'] }}</div>
                                        </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Action Buttons -->
                            <div class="flex sm:flex-col gap-2 sm:gap-1">
                                <button onclick="editRule({{ $rule->id }})" class="p-2 text-gray-600 hover:text-gray-900 hover:bg-gray-50 rounded-lg transition border border-gray-200">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button onclick="deleteRule({{ $rule->id }})" class="p-2 text-gray-600 hover:text-red-600 hover:bg-red-50 rounded-lg transition border border-gray-200">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    @empty
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add/Edit Rule Modal -->
<div id="ruleModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50 hidden">
    <div class="bg-white rounded-lg shadow-xl max-w-2xl w-full max-h-[90vh] overflow-y-auto">
        <div class="border-b border-gray-100 px-6 py-4">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-semibold text-gray-900" id="modalTitle">Add New Rule</h3>
                <button type="button" onclick="hideRuleModal()" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </div>
        <div class="p-6">
            <div class="space-y-6">
                <!-- Rule Basic Info -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Rule Name *</label>
                        <input type="text" id="ruleName"
                                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-900 focus:border-indigo-900 outline-none transition"
                                placeholder="e.g., Improve Grade B to A">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Priority *</label>
                        <input type="number" id="rulePriority" min="1" max="100" value="1"
                                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-900 focus:border-indigo-900 outline-none transition">
                    </div>
                </div>

                <!-- Conditions Section -->
                <div class="border-t border-gray-100 pt-6">
                    <div class="flex justify-between items-center mb-4">
                        <div>
                            <h4 class="font-medium text-gray-900">Conditions</h4>
                            <p class="text-sm text-gray-600 mt-1">Define when this rule should trigger</p>
                        </div>
                        <button type="button" onclick="addCondition()" class="text-sm text-indigo-900 hover:text-indigo-700 font-medium flex items-center gap-1">
                            <i class="fas fa-plus"></i>
                            Add Condition
                        </button>
                    </div>
                    <div id="conditionsContainer" class="space-y-3">
                        <!-- Conditions will be added here -->
                    </div>
                </div>

                <!-- Actions Section -->
                <div class="border-t border-gray-100 pt-6">
                    <div class="flex justify-between items-center mb-4">
                        <div>
                            <h4 class="font-medium text-gray-900">Suggested Activities</h4>
                            <p class="text-sm text-gray-600 mt-1">Activities to suggest when conditions are met</p>
                        </div>
                        <button type="button" onclick="addAction()" class="text-sm text-indigo-900 hover:text-indigo-700 font-medium flex items-center gap-1">
                            <i class="fas fa-plus"></i>
                            Add Activity
                        </button>
                    </div>
                    <div id="actionsContainer" class="space-y-3">
                        <!-- Actions will be added here -->
                    </div>
                </div>

                <!-- Additional Settings -->
                <div class="border-t border-gray-100 pt-6">
                    <div class="flex items-center gap-3">
                        <input type="checkbox" id="ruleActive" checked class="rounded border-gray-300 text-indigo-900 focus:ring-indigo-900">
                        <div>
                            <label for="ruleActive" class="text-sm font-medium text-gray-700">Rule is active</label>
                            <p class="text-xs text-gray-500 mt-1">Inactive rules won't be evaluated</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="border-t border-gray-100 px-6 py-4 flex justify-end gap-3">
            <button type="button" onclick="hideRuleModal()" class="px-5 py-2.5 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition font-medium">
                Cancel
            </button>
            <button type="button" onclick="saveRule()" id="saveRuleBtn" class="px-5 py-2.5 bg-indigo-900 text-white rounded-lg hover:bg-indigo-800 transition font-medium">
                Save Rule
            </button>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // CSRF Token for AJAX requests
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    let editingRuleId = null;

    const conditionTypes = [
        { value: "grade", label: "Current Grade", inputType: "select", options: ["A", "B", "C", "D", "E"] },
        { value: "score", label: "Current Score", inputType: "number" },
        { value: "weak_component", label: "Weak Component", inputType: "select", options: ["uniform", "club", "sport", "competition"] },
        { value: "has_activity", label: "Has Activity Type", inputType: "select", options: ["uniform", "club", "sport", "competition"] },
        { value: "missing_activity", label: "Missing Activity Type", inputType: "select", options: ["uniform", "club", "sport", "competition"] }
    ];

    const operators = [
        { value: "equals", label: "Equals" },
        { value: "greater_than", label: "Greater Than" },
        { value: "less_than", label: "Less Than" },
        { value: "greater_or_equal", label: "Greater or Equal" },
        { value: "less_or_equal", label: "Less or Equal" },
        { value: "not_equals", label: "Not Equals" }
    ];

    function showAddRuleModal() {
        editingRuleId = null;
        document.getElementById('modalTitle').textContent = 'Add New Rule';
        document.getElementById('saveRuleBtn').textContent = 'Save Rule';

        document.getElementById('ruleName').value = '';
        document.getElementById('rulePriority').value = {{ $rules->count() + 1 }};
        document.getElementById('ruleActive').checked = true;
        document.getElementById('conditionsContainer').innerHTML = '';
        document.getElementById('actionsContainer').innerHTML = '';

        addCondition();
        addAction();

        document.getElementById('ruleModal').classList.remove('hidden');
    }

    function hideRuleModal() {
        document.getElementById('ruleModal').classList.add('hidden');
    }

    function editRule(id) {
        editingRuleId = id;
        document.getElementById('modalTitle').textContent = 'Edit Rule';
        document.getElementById('saveRuleBtn').textContent = 'Update Rule';

        fetch(`/admin/api/suggestions/rules/${id}`, {
            headers: { 'Accept': 'application/json' }
        })
        .then(response => response.json())
        .then(data => {
            if (!data.success) {
                showToast(data.message || 'Failed to load rule', 'error');
                return;
            }

            const rule = data.rule;

            document.getElementById('ruleName').value = rule.name;
            document.getElementById('rulePriority').value = rule.priority;
            document.getElementById('ruleActive').checked = !!rule.active;

            const conditionsContainer = document.getElementById('conditionsContainer');
            conditionsContainer.innerHTML = '';
            (rule.conditions || []).forEach(cond => addCondition(cond));

            const actionsContainer = document.getElementById('actionsContainer');
            actionsContainer.innerHTML = '';
            (rule.actions || []).forEach(action => addAction(action));

            document.getElementById('ruleModal').classList.remove('hidden');
        })
        .catch(error => {
            console.error(error);
            showToast('An error occurred while loading the rule', 'error');
        });
    }

    function addCondition(condition = null) {
        const container = document.getElementById('conditionsContainer');
        const conditionId = Date.now() + Math.random();

        let html = `
            <div id="condition-${conditionId}" class="flex items-start gap-3 p-4 bg-gray-50 rounded-lg border border-gray-200">
                <div class="flex-1 grid grid-cols-1 sm:grid-cols-3 gap-3">
                    <div>
                        <select class="condition-type w-full px-3 py-2 border border-gray-300 rounded text-sm focus:ring-2 focus:ring-indigo-900 focus:border-indigo-900 outline-none bg-white">
                            ${conditionTypes.map(ct => `
                                <option value="${ct.value}">${ct.label}</option>
                            `).join('')}
                        </select>
                    </div>
                    <div>
                        <select class="condition-operator w-full px-3 py-2 border border-gray-300 rounded text-sm focus:ring-2 focus:ring-indigo-900 focus:border-indigo-900 outline-none bg-white">
                            ${operators.map(op => `
                                <option value="${op.value}">${op.label}</option>
                            `).join('')}
                        </select>
                    </div>
                    <div>
                        <div class="condition-value-container">
                            <input type="text" class="condition-value w-full px-3 py-2 border border-gray-300 rounded text-sm focus:ring-2 focus:ring-indigo-900 focus:border-indigo-900 outline-none bg-white" placeholder="Value">
                        </div>
                    </div>
                </div>
                <button type="button" onclick="this.parentElement.remove()" class="p-2 text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        `;

        container.insertAdjacentHTML('beforeend', html);

        const conditionElement = document.getElementById(`condition-${conditionId}`);
        const typeSelect = conditionElement.querySelector('.condition-type');
        const valueContainer = conditionElement.querySelector('.condition-value-container');

        typeSelect.addEventListener('change', function () {
            updateConditionValueInput(this.value, valueContainer);
        });

        const initialType = condition ? condition.type : conditionTypes[0].value;
        updateConditionValueInput(initialType, valueContainer);

        if (condition) {
            conditionElement.querySelector('.condition-type').value = condition.type;
            conditionElement.querySelector('.condition-operator').value = condition.operator;

            setTimeout(() => {
                const valueInput = conditionElement.querySelector('.condition-value');
                if (valueInput) valueInput.value = condition.value;
            }, 10);
        }
    }

    function updateConditionValueInput(conditionType, container) {
        const conditionTypeObj = conditionTypes.find(ct => ct.value === conditionType);
        if (!conditionTypeObj) {
            container.innerHTML = `
                <input type="text" class="condition-value w-full px-3 py-2 border border-gray-300 rounded text-sm focus:ring-2 focus:ring-indigo-900 focus:border-indigo-900 outline-none bg-white" placeholder="Enter value">
            `;
            return;
        }

        if (conditionTypeObj.inputType === 'select' && conditionTypeObj.options) {
            container.innerHTML = `
                <select class="condition-value w-full px-3 py-2 border border-gray-300 rounded text-sm focus:ring-2 focus:ring-indigo-900 focus:border-indigo-900 outline-none bg-white">
                    ${conditionTypeObj.options.map(opt => {
                        const label = opt === 'uniform' ? 'Uniform Body'
                                     : opt === 'club' ? 'Club & Society'
                                     : opt === 'sport' ? 'Sports & Games'
                                     : opt === 'competition' ? 'Competition'
                                     : opt;
                        return `<option value="${opt}">${label}</option>`;
                    }).join('')}
                </select>
            `;
        } else if (conditionTypeObj.inputType === 'number') {
            container.innerHTML = `
                <input type="number" class="condition-value w-full px-3 py-2 border border-gray-300 rounded text-sm focus:ring-2 focus:ring-indigo-900 focus:border-indigo-900 outline-none bg-white"
                       placeholder="0-100" min="0" max="100" step="1">
            `;
        } else {
            container.innerHTML = `
                <input type="text" class="condition-value w-full px-3 py-2 border border-gray-300 rounded text-sm focus:ring-2 focus:ring-indigo-900 focus:border-indigo-900 outline-none bg-white"
                       placeholder="Enter value">
            `;
        }
    }

    function addAction(action = null) {
        const container = document.getElementById('actionsContainer');
        const actionId = Date.now() + Math.random();

        let html = `
            <div id="action-${actionId}" class="p-4 bg-gray-50 rounded-lg border border-gray-200">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1.5">Activity Type</label>
                        <select class="action-type w-full px-3 py-2 border border-gray-300 rounded text-sm focus:ring-2 focus:ring-indigo-900 focus:border-indigo-900 outline-none bg-white">
                            <option value="uniform">Uniform Body</option>
                            <option value="club">Club & Society</option>
                            <option value="sport">Sports & Games</option>
                            <option value="competition">Competition</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1.5">Activity Name</label>
                        <input type="text" class="action-name w-full px-3 py-2 border border-gray-300 rounded text-sm focus:ring-2 focus:ring-indigo-900 focus:border-indigo-900 outline-none bg-white"
                               placeholder="e.g., Volleyball Tournament">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1.5">Level</label>
                        <select class="action-level w-full px-3 py-2 border border-gray-300 rounded text-sm focus:ring-2 focus:ring-indigo-900 focus:border-indigo-900 outline-none bg-white">
                            <option value="school">School</option>
                            <option value="district">District</option>
                            <option value="state">State</option>
                            <option value="national">National</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1.5">Achievement</label>
                        <select class="action-achievement w-full px-3 py-2 border border-gray-300 rounded text-sm focus:ring-2 focus:ring-indigo-900 focus:border-indigo-900 outline-none bg-white">
                            <option value="participation">Participation</option>
                            <option value="third">Third Place</option>
                            <option value="second">Runner-Up</option>
                            <option value="first">Champion</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1.5">Points</label>
                        <input type="number" class="action-points w-full px-3 py-2 border border-gray-300 rounded text-sm focus:ring-2 focus:ring-indigo-900 focus:border-indigo-900 outline-none bg-white"
                               value="5" min="1" max="20">
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-xs font-medium text-gray-700 mb-1.5">Message</label>
                        <textarea class="action-message w-full px-3 py-2 border border-gray-300 rounded text-sm focus:ring-2 focus:ring-indigo-900 focus:border-indigo-900 outline-none bg-white"
                                  placeholder="Explain why this helps the student..." rows="2"></textarea>
                    </div>
                </div>
                <div class="flex justify-end">
                    <button type="button" onclick="this.parentElement.parentElement.remove()" class="text-xs text-gray-500 hover:text-gray-700 flex items-center gap-1">
                        <i class="fas fa-trash-alt"></i>
                        Remove
                    </button>
                </div>
            </div>
        `;

        container.insertAdjacentHTML('beforeend', html);

        if (action) {
            const element = document.getElementById(`action-${actionId}`);
            element.querySelector('.action-type').value = action.activityType || 'uniform';
            element.querySelector('.action-name').value = action.activityName || '';
            element.querySelector('.action-level').value = action.level || 'school';
            element.querySelector('.action-achievement').value = action.achievement || 'participation';
            element.querySelector('.action-points').value = action.points || 5;
            element.querySelector('.action-message').value = action.message || '';
        }
    }

    function saveRule() {
        const name = document.getElementById('ruleName').value.trim();
        const priority = parseInt(document.getElementById('rulePriority').value);
        const active = document.getElementById('ruleActive').checked;

        if (!name) {
            showToast('Please enter a rule name', 'error');
            return;
        }

        if (isNaN(priority) || priority < 1 || priority > 100) {
            showToast('Please enter a valid priority between 1-100', 'error');
            return;
        }

        const conditions = [];
        document.querySelectorAll('#conditionsContainer > div').forEach(conditionDiv => {
            const type = conditionDiv.querySelector('.condition-type').value;
            const operator = conditionDiv.querySelector('.condition-operator').value;
            const value = conditionDiv.querySelector('.condition-value').value;

            if (type && operator && value !== '') {
                conditions.push({ type, operator, value });
            }
        });

        if (conditions.length === 0) {
            showToast('Please add at least one condition', 'error');
            return;
        }

        const actions = [];
        let invalidAction = false;
        document.querySelectorAll('#actionsContainer > div').forEach(actionDiv => {
            const activityName = actionDiv.querySelector('.action-name').value.trim();
            const message = actionDiv.querySelector('.action-message').value.trim();

            if (!activityName) {
                invalidAction = true;
                return;
            }

            actions.push({
                type: "suggest_activity",
                activityType: actionDiv.querySelector('.action-type').value,
                activityName: activityName,
                level: actionDiv.querySelector('.action-level').value,
                achievement: actionDiv.querySelector('.action-achievement').value,
                points: parseInt(actionDiv.querySelector('.action-points').value),
                message: message || "Take this opportunity to improve your PAJSK score!"
            });
        });

        if (invalidAction) {
            showToast('Please enter activity name for all activities', 'error');
            return;
        }

        if (actions.length === 0) {
            showToast('Please add at least one activity', 'error');
            return;
        }

        const payload = {
            name,
            priority,
            active,
            conditions,
            actions
        };

        const url = editingRuleId
            ? `/admin/api/suggestions/rules/${editingRuleId}`
            : '/admin/api/suggestions/rules';
        const method = editingRuleId ? 'PUT' : 'POST';

        fetch(url, {
            method,
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            },
            body: JSON.stringify(payload)
        })
        .then(response => response.json())
        .then(data => {
            if (!data.success) {
                showToast('Failed to save rule', 'error');
                console.error(data.errors || data.message);
                return;
            }

            showToast(data.message || 'Rule saved successfully', 'success');
            hideRuleModal();
            setTimeout(() => {
                window.location.reload();
            }, 800);
        })
        .catch(error => {
            console.error(error);
            showToast('An error occurred while saving the rule', 'error');
        });
    }
    
    function deleteRule(id) {
        if (confirm('Are you sure you want to delete this rule? This action cannot be undone.')) {
            fetch(`/admin/api/suggestions/rules/${id}`, {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showToast(data.message, 'success');
                    // Reload page after 1 second
                    setTimeout(() => {
                        window.location.reload();
                    }, 1000);
                } else {
                    showToast(data.message || 'Failed to delete rule', 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showToast('An error occurred while deleting the rule', 'error');
            });
        }
    }
</script>
@endpush