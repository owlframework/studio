<?php
// Prevent direct script access by verifying application root definition.
defined('ROOTPATH') OR die("Direct access denied.");
 
// 1. Register Exception Handler
set_exception_handler(function($exception) {
    owl_render_error(
        get_class($exception),
        $exception->getMessage(),
        $exception->getFile(),
        $exception->getLine(),
        $exception->getTrace()
    );
});

// 2. Register Error Handler
set_error_handler(function($severity, $message, $file, $line) {
    if (!(error_reporting() & $severity)) {
        return;
    }
    
    $severityName = match($severity) {
        E_USER_ERROR   => 'User Error',
        E_USER_WARNING => 'User Warning',
        E_WARNING      => 'Warning',
        E_NOTICE       => 'Notice',
        default        => 'PHP Error'
    };

    owl_render_error($severityName, $message, $file, $line, debug_backtrace());
    exit;
});

// 3. Register Shutdown Handler for Fatal Errors
register_shutdown_function(function() {
    $error = error_get_last();
    if ($error !== null && in_array($error['type'], [E_ERROR, E_CORE_ERROR, E_COMPILE_ERROR, E_PARSE])) {
        owl_render_error(
            'Fatal Error',
            $error['message'],
            $error['file'],
            $error['line'],
            []
        );
    }
});

/**
 * Renders the clean dark-mode error screen (CSP Compliant)
 */
function owl_render_error($type, $message, $file, $line, $trace) {
    if (ob_get_level()) {
        ob_clean();
    }

    http_response_code(500);

    $nonce = function_exists('csp_nonce') ? csp_nonce() : (function_exists('get_nonce') ? get_nonce() : '');
    $isDebug = (defined('DEBUG') && DEBUG);

    ?>
   <!DOCTYPE html>
<html lang="en" data-theme="dark">
<head>
    <meta charset="UTF-8">
    <title>Application Error</title>
     
    <style nonce="<?= get_nonce();?>">
        /* ==========================================================================
           OWL FRAMEWORK ERROR PAGE DESIGN TOKENS (Inline Styling)
           ========================================================================== */
        :root {
          --brand-gold: #f4b223;
          --brand-gold-glow: rgba(244, 178, 35, 0.25);
          --bg-main: #0b0c0e;
          --bg-surface: #121418;
          --bg-surface-alt: #181b21;
          --border-color: #22262f;
          --text-main: #f9fafb;
          --text-muted: #9ca3af;
          --text-subtle: #6b7280;
          --code-bg: #090a0c;
          --code-text: #f3f4f6;
          --code-border: #242934;
          --radius-md: 10px;
          --radius-lg: 16px;
          --font-sans: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
          --font-mono: "Fira Code", "Cascadia Code", Consolas, "Courier New", monospace;
          --shadow-lg: 0 12px 32px rgba(0, 0, 0, 0.6);
        }

        *, *::before, *::after {
          box-sizing: border-box;
          margin: 0;
          padding: 0;
        }

        body {
          font-family: var(--font-sans);
          background-color: var(--bg-main);
          color: var(--text-main);
          line-height: 1.6;
          padding: 40px 20px;
          display: flex;
          justify-content: center;
          align-items: center;
          min-height: 100vh;
        }

        .owl-error-container {
          width: 100%;
          max-width: 900px;
          background: var(--bg-surface);
          border: 1px solid var(--border-color);
          border-radius: var(--radius-lg);
          box-shadow: var(--shadow-lg);
          overflow: hidden;
        }

        .owl-error-header {
          background: var(--bg-surface-alt);
          padding: 2rem;
          border-bottom: 1px solid var(--border-color);
          border-left: 5px solid #ef4444; /* Error red accent */
        }

        .owl-error-header h1 {
          font-size: 1.5rem;
          font-weight: 800;
          color: #ef4444;
          margin-bottom: 0.5rem;
          font-family: var(--font-mono);
        }

        .owl-error-header p {
          font-size: 1.05rem;
          color: var(--text-main);
          word-break: break-word;
        }

        .owl-error-body {
          padding: 2rem;
        }

        .owl-section-title {
          font-size: 0.85rem;
          font-weight: 700;
          color: var(--brand-gold);
          text-transform: uppercase;
          letter-spacing: 0.08em;
          margin-bottom: 0.75rem;
          margin-top: 1.5rem;
        }

        .owl-section-title:first-child {
          margin-top: 0;
        }

        .owl-error-location {
          background: var(--code-bg);
          border: 1px solid var(--code-border);
          border-radius: var(--radius-md);
          padding: 1rem 1.25rem;
          font-family: var(--font-mono);
          font-size: 0.9rem;
          color: var(--code-text);
          line-height: 1.7;
        }

        .owl-error-location span {
          color: var(--brand-gold);
        }

        .owl-trace-list {
          list-style: none;
          background: var(--code-bg);
          border: 1px solid var(--code-border);
          border-radius: var(--radius-md);
          max-height: 350px;
          overflow-y: auto;
          padding: 0.5rem 0;
        }

        .owl-trace-item {
          padding: 0.6rem 1.25rem;
          font-family: var(--font-mono);
          font-size: 0.8125rem;
          color: var(--text-muted);
          border-bottom: 1px solid var(--code-border);
          word-break: break-all;
        }

        .owl-trace-item:last-child {
          border-bottom: none;
        }

        .owl-trace-func {
          color: #60a5fa;
          font-weight: 600;
        }

        .owl-error-footer {
          background: var(--bg-surface-alt);
          padding: 1rem 2rem;
          border-top: 1px solid var(--border-color);
          text-align: center;
          font-size: 0.8rem;
          color: var(--text-subtle);
          font-family: var(--font-mono);
        }
    </style>
</head>
<body>
    <div class="owl-error-container">
            <div class="owl-error-header">
                <h1><?= esc($isDebug ? $type : 'Application Error'); ?></h1>
                <p><?= esc($isDebug ? $message : 'An unexpected error occurred on our servers. Please try again later.'); ?></p>
            </div>
            
            <div class="owl-error-body">
                <?php if ($isDebug): ?>
                    <h3 class="owl-section-title">Error Location</h3>
                    <div class="owl-error-location">
                        File: <span><?= esc($file); ?></span><br>
                        Line: <span><?= (int)$line; ?></span>
                    </div>

                    <?php if (!empty($trace)): ?>
                        <h3 class="owl-section-title">Stack Trace</h3>
                        <ul class="owl-trace-list">
                            <?php foreach ($trace as $index => $t): ?>
                                <li class="owl-trace-item">
                                    #<?= $index; ?> 
                                    <?= isset($t['file']) ? esc($t['file']) : '[internal function]'; ?> 
                                    <?= isset($t['line']) ? '(Line: ' . $t['line'] . ')' : ''; ?> — 
                                    <span class="owl-trace-func"><?= isset($t['function']) ? esc($t['function']) : ''; ?></span>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>
                <?php else: ?>
                    <p class="owl-production-msg">Our technical team has been notified. If this problem persists, please reach out to support.</p>
                <?php endif; ?>
            </div>

          
        </div>
</body>
</html>
    <?php
    exit;
}
?>