<?php

declare(strict_types=1);

namespace Auth0\Quickstart;

final class ApplicationTemplates
{
    /**
     * An instance of our Quickstart Application.
     */
    private Application $app;

    /**
     * State machine of the template being rendered.
     *
     * @param array<mixed>
     */
    protected array $state;

    /**
     * ApplicationTemplates constructor.
     *
     * @param Application $app An instance of our Quickstart Application.
     */
    public function __construct(
        Application &$app
    ) {
        $this->app = & $app;

        $this->reset();
    }

    /**
     * Render a template as the browser response, then exit.
     *
     * @param string $template The name of the template to use.
     * @param mixed $variables Any variables the template should have access to use.
     */
    final public function render(
        string $template,
        ... $variables
    ) {
        $this->reset();

        echo $this->renderTemplate($template, $variables);
        exit;
    }

    /**
     * Reset the state of the template engine, to prepare for a fresh render.
     */
    final public function reset()
    {
        $this->state['sections'] = [];
        $this->state['section'] = null;
        $this->state['layout'] = null;
    }

    /**
     * Render a template, and return the content as a string.
     *
     * @param string $template The name of the template to use.
     * @param array $variables Any variables the template should have access to use.
     */
    final protected function renderTemplate(
        string $template,
        array $variables
    ): string {
        // Keep track of the output buffering 'level'.
        $level = 0;

        // Resolve the requested template to it's file path:
        $template = join(DIRECTORY_SEPARATOR, [APP_ROOT, 'templates', $template . '.php']);

        // Extract $variables into current scope, for use in template.
        extract($variables);

        try {
            $level = ob_get_level();
            ob_start();

            include $template;

            $content = ob_get_clean();

            if ($this->state['layout'] !== null) {
                $layoutTemplate = $this->state['layout']['name'];
                $layoutVariables = array_merge($variables, $this->state['layout']['variables']);

                $this->state['sections']['content'] = $content;
                $this->state['layout'] = null;

                $content = $this->renderTemplate(
                    template: $layoutTemplate,
                    variables: $layoutVariables
                );
            }

            return trim($content);
        } catch (\Throwable $e) {
            while (ob_get_level() > $level) {
                ob_end_clean();
            }

            throw $e;
        }
    }

    /**
     * Render a section into the template, if it's content has been set.
     *
     * @param string $sectionName Name of the section to render into the template.
     */
    final protected function section(
        string $sectionName
    ): string {
        return $this->state['sections'][$sectionName] ?? '';
    }

    /**
     * Start capturing a template block as new section content.
     *
     * @param string $sectionName Name of the section to begin capturing.
     */
    final protected function start(
        string $sectionName
    ) {
        if ($this->state['section'] !== null) {
            throw new \LogicException('Nested sections are not supported.');
        }

        $this->state['section'] = $sectionName;

        ob_start();
    }

    /**
     * Stop capturing a previously started template block, and store the section content for use.
     */
    final protected function stop() {
        if ($this->state['section'] === null) {
            throw new \LogicException('You must start a section before stopping it.');
        }

        if (isset($this->state['sections'][$this->state['section']])) {
            $this->state['sections'][$this->state['section']] = '';
        }

        $this->state['sections'][$this->state['section']] = ob_get_clean();
        $this->state['section'] = null;
    }

    /**
     * Define a container layout in which to render a template.
     *
     * @param string $name The name of the layout template to use.
     * @param mixed $variables Any additional variables the layout template should have access to use.
     */
    final protected function layout(
        string $name,
        ... $variables
    ) {
        $this->state['layout'] = [
            'name' => $name,
            'variables' => $variables
        ];
    }
}
