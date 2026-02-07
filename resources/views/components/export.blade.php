@props(['type' => 'students', 'filters' => []])

<div x-data="exportManager()" class="relative">
    <!-- Export Button -->
    <button @click="showExportMenu = !showExportMenu" class="btn-outline text-xs flex items-center gap-2">
        <svg class="h-3 w-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4M7 10l5 5 5-5M12 15V3" />
        </svg>
        Export
        <svg class="h-3 w-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M6 9l6 6 6-6" />
        </svg>
    </button>
    
    <!-- Export Menu -->
    <div class="absolute top-full right-0 mt-2 w-64 bg-white dark:bg-dark-100 rounded-lg shadow-lg border border-gray-100 dark:border-dark-200 z-10" 
         x-show="showExportMenu" 
         x-transition
         @click.away="showExportMenu = false">
        
        <div class="p-4">
            <h3 class="text-sm font-semibold text-gray-900 dark:text-dark-900 mb-3">Export Options</h3>
            
            <!-- Format Selection -->
            <div class="space-y-2 mb-4">
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="radio" name="format" value="csv" x-model="selectedFormat" class="checkbox-custom">
                    <span class="text-sm text-gray-700 dark:text-dark-300">CSV (Excel Compatible)</span>
                </label>
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="radio" name="format" value="excel" x-model="selectedFormat" class="checkbox-custom">
                    <span class="text-sm text-gray-700 dark:text-dark-300">Excel (.xlsx)</span>
                </label>
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="radio" name="format" value="pdf" x-model="selectedFormat" class="checkbox-custom">
                    <span class="text-sm text-gray-700 dark:text-dark-300">PDF Report</span>
                </label>
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="radio" name="format" value="json" x-model="selectedFormat" class="checkbox-custom">
                    <span class="text-sm text-gray-700 dark:text-dark-300">JSON (Data)</span>
                </label>
            </div>
            
            <!-- Date Range -->
            <div class="mb-4">
                <label class="block text-xs font-semibold text-gray-700 dark:text-dark-300 mb-2">Date Range</label>
                <div class="grid grid-cols-2 gap-2">
                    <input type="date" x-model="dateFrom" class="input-compact text-xs" placeholder="From">
                    <input type="date" x-model="dateTo" class="input-compact text-xs" placeholder="To">
                </div>
            </div>
            
            <!-- Include Options -->
            <div class="mb-4">
                <label class="block text-xs font-semibold text-gray-700 dark:text-dark-300 mb-2">Include</label>
                <div class="space-y-1">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" x-model="includeHeaders" class="checkbox-custom">
                        <span class="text-xs text-gray-600 dark:text-dark-400">Headers</span>
                    </label>
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" x-model="includeFilters" class="checkbox-custom">
                        <span class="text-xs text-gray-600 dark:text-dark-400">Current Filters</span>
                    </label>
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" x-model="includeMetadata" class="checkbox-custom">
                        <span class="text-xs text-gray-600 dark:text-dark-400">Metadata</span>
                    </label>
                </div>
            </div>
            
            <!-- Action Buttons -->
            <div class="flex gap-2">
                <button @click="performExport" class="btn-primary text-xs flex-1">
                    Export Now
                </button>
                <button @click="scheduleExport" class="btn-outline text-xs flex-1">
                    Schedule
                </button>
            </div>
        </div>
    </div>
</div>

<script>
function exportManager() {
    return {
        showExportMenu: false,
        selectedFormat: 'csv',
        dateFrom: '',
        dateTo: '',
        includeHeaders: true,
        includeFilters: false,
        includeMetadata: false,
        
        performExport() {
            const options = {
                format: this.selectedFormat,
                dateFrom: this.dateFrom,
                dateTo: this.dateTo,
                includeHeaders: this.includeHeaders,
                includeFilters: this.includeFilters,
                includeMetadata: this.includeMetadata
            };
            
            // Show progress
            const progress = showProgressNotification('Preparing export...', 0);
            
            // Simulate export progress
            let currentProgress = 0;
            const interval = setInterval(() => {
                currentProgress += Math.random() * 30;
                if (currentProgress >= 100) {
                    currentProgress = 100;
                    clearInterval(interval);
                    
                    // Complete export
                    progress.updateProgress(100);
                    showSuccessNotification(
                        `Export completed!`, 
                        () => this.downloadFile(options.format),
                        'Download'
                    );
                    
                    this.showExportMenu = false;
                } else {
                    progress.updateProgress(currentProgress);
                }
            }, 500);
        },
        
        scheduleExport() {
            showNotification('Export scheduled for daily at 2:00 AM', 'success');
            this.showExportMenu = false;
        },
        
        downloadFile(format) {
            // Create sample file based on format
            const filename = `export_${new Date().toISOString().split('T')[0]}.${format}`;
            
            if (format === 'csv') {
                this.downloadCSV(filename);
            } else if (format === 'json') {
                this.downloadJSON(filename);
            } else {
                showNotification(`Downloading ${filename}...`, 'info');
            }
        },
        
        downloadCSV(filename) {
            const csv = 'Name,Email,Class,Status\nJohn Doe,john@example.com,JSS1,Active\nJane Smith,jane@example.com,JSS2,Active';
            const blob = new Blob([csv], { type: 'text/csv' });
            this.downloadBlob(blob, filename);
        },
        
        downloadJSON(filename) {
            const json = JSON.stringify([
                { name: 'John Doe', email: 'john@example.com', class: 'JSS1', status: 'Active' },
                { name: 'Jane Smith', email: 'jane@example.com', class: 'JSS2', status: 'Active' }
            ], null, 2);
            const blob = new Blob([json], { type: 'application/json' });
            this.downloadBlob(blob, filename);
        },
        
        downloadBlob(blob, filename) {
            const url = window.URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = filename;
            document.body.appendChild(a);
            a.click();
            document.body.removeChild(a);
            window.URL.revokeObjectURL(url);
        }
    }
}
</script>