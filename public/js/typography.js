/**
 * Modestik Automated Bangla & English Typography Detection Engine
 */
(function() {
    const bnRegex = /[\u0980-\u09FF]/;

    function applyTypography(node) {
        if (!node || node.nodeType !== 1) return;

        // Skip script and style tags
        const tagName = node.tagName.toLowerCase();
        if (tagName === 'script' || tagName === 'style' || tagName === 'svg' || tagName === 'code' || tagName === 'i') {
            return;
        }

        // Check text content or placeholder
        if (node.placeholder && bnRegex.test(node.placeholder)) {
            node.classList.add('bn-text', 'bn-placeholder');
        }

        if (node.children.length === 0 && node.textContent && bnRegex.test(node.textContent)) {
            node.classList.add('bn-text');
        } else {
            // Check direct text nodes or children
            for (let i = 0; i < node.childNodes.length; i++) {
                const child = node.childNodes[i];
                if (child.nodeType === 3 && bnRegex.test(child.textContent)) {
                    node.classList.add('bn-text');
                    break;
                }
            }
        }
    }

    function scanContainer(container) {
        const elements = container.querySelectorAll('h1, h2, h3, h4, h5, h6, p, span, a, label, button, input, textarea, select, th, td, small, div.card-title, div.page-title, div.section-title, .breadcrumb-item, .nav-link, .badge');
        elements.forEach(applyTypography);
    }

    document.addEventListener('DOMContentLoaded', function() {
        scanContainer(document.body);

        // Dynamic mutation observer for dynamically loaded content or AJAX
        const observer = new MutationObserver(function(mutations) {
            mutations.forEach(function(mutation) {
                mutation.addedNodes.forEach(function(addedNode) {
                    if (addedNode.nodeType === 1) {
                        applyTypography(addedNode);
                        scanContainer(addedNode);
                    }
                });
            });
        });

        observer.observe(document.body, { childList: true, subtree: true });
    });
})();
