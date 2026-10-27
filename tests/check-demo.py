from pathlib import Path
from html.parser import HTMLParser
from urllib.parse import urlsplit, unquote

root = Path(__file__).resolve().parents[1] / '.pages'
count = 0
class Check(HTMLParser):
    def __init__(self, page):
        super().__init__()
        self.page = page
    def handle_starttag(self, tag, attributes):
        global count
        attrs = dict(attributes)
        for attribute in ('href', 'src'):
            value = attrs.get(attribute, '')
            parsed = urlsplit(value)
            if not value or parsed.scheme or parsed.netloc or not parsed.path:
                continue
            assert not parsed.path.endswith('.php'), (self.page, value)
            target = (self.page.parent / unquote(parsed.path)).resolve()
            assert target.is_relative_to(root.resolve()), (self.page, value)
            assert target.is_file(), (self.page, value)
            count += 1

pages = list(root.rglob('*.html'))
assert len(pages) >= 5
for page in pages:
    html = page.read_text(encoding='utf-8')
    assert '<?php' not in html and '<?=' not in html
    assert 'Warning:' not in html and 'Fatal error' not in html
    Check(page).feed(html)
assert not list(root.rglob('*.php'))
assert not list(root.rglob('*.sql'))
assert not list(root.rglob('local.php'))
print(f'{len(pages)} static pages, {count} local links and assets verified')
