<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Helpers\HtmlPurifierHelper;

class HtmlPurifierTest extends TestCase
{

    #[Test]
    public function it_removes_script_tags()
    {
        $dirty = '<p>Hello</p><script>alert("XSS")</script><p>World</p>';
        $clean = HtmlPurifierHelper::clean($dirty);
        
        $this->assertStringNotContainsString('<script', $clean);
        $this->assertStringContainsString('<p>Hello</p>', $clean);
        $this->assertStringContainsString('<p>World</p>', $clean);
    }
    
    #[Test]
    public function it_removes_event_handlers()
    {
        $dirty = '<img src="image.jpg" onclick="alert(\'XSS\')">';
        $clean = HtmlPurifierHelper::clean($dirty);
        
        $this->assertStringNotContainsString('onclick', $clean);
        $this->assertStringContainsString('src="image.jpg"', $clean);
    }
    
    #[Test]
    public function it_allows_safe_html_tags()
    {
        $html = '<h1>Title</h1><p>Paragraph</p><strong>Bold</strong><em>Italic</em>';
        $clean = HtmlPurifierHelper::clean($html);
        
        $this->assertEquals($html, $clean);
    }

    #[Test]
    public function it_removes_iframes()
    {
        $dirty = '<iframe src="https://evil.com"></iframe>';
        $clean = HtmlPurifierHelper::clean($dirty);
        
        $this->assertStringNotContainsString('<iframe', $clean);
    }
}