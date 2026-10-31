<div class="mf-step mf-step-2" :class="{ 'mf-active': currentStepName === 'Issues' }">
    <h2 class="mf-title">What is the most important issue that you are facing?</h2>
    <p class="mf-subtitle">
        Pick your top concern<span class="required">*</span>
    </p>
    <div class="mf-preference-grid mf-grid mf-grid-issues mf-checkbox-grid">
        <div
                class="mf-preference-option"
                v-for="issue in filteredIssues"
                :key="issue.tag"
        >
            <input
                    type="checkbox"
                    :id="`mf-opt-${issue.tag}`"
                    :value="issue.tag"
                    v-model="selectedIssues"
                    @change="onIssuesSelect"

            />
            <label :for="`mf-opt-${issue.tag}`">
                {{ issue.name }}
            </label>
        </div>
    </div>
    <div class="mf-step-2-show-more-btn">
        <button class="mf-btn mf-btn-outline-primary mf-action-button" @click="toggleShowAll" v-if="!showAll">Show More</button>
    </div>

</div>
