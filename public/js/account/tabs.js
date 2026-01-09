(function(){
  function qs(selector, root){ return (root||document).querySelector(selector); }
  function initAjaxLinks(){
    document.querySelectorAll('a.ajax-link').forEach(function(link){
      // Avoid duplicate binding
      if(link.dataset.ajaxBound === '1') return;
      link.dataset.ajaxBound = '1';
      link.addEventListener('click', function(e){
        if(e.button !== 0 || e.metaKey || e.ctrlKey || e.shiftKey || e.altKey) return;
        e.preventDefault();
        const url = link.getAttribute('href');
        if(!url) return;
        const column = qs('.col-md-9');
        const card = qs('.col-md-9 .card.content-border');
        const cardBody = qs('.col-md-9 .card.content-border .card-body');
        const container = cardBody || card || column;
        if(!container){ window.location.href = url; return; }
        // Preserve height to prevent layout jump
        const h = container.offsetHeight;
        container.style.minHeight = h + 'px';
        container.style.opacity = '0';
        fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' }})
          .then(function(res){ return res.text(); })
          .then(function(html){
            const parser = new DOMParser();
            const doc = parser.parseFromString(html, 'text/html');
            // Inject head <style> from response so page-specific CSS applies
            try {
              const styles = doc.querySelectorAll('head style');
              styles.forEach(function(s){
                // avoid duplicates by content
                const text = s.textContent || '';
                const exists = Array.from(document.head.querySelectorAll('style')).some(function(hs){ return hs.textContent === text; });
                if(!exists){
                  const clone = document.createElement('style');
                  clone.textContent = text;
                  clone.setAttribute('data-ajax-injected', '1');
                  document.head.appendChild(clone);
                }
              });
            } catch (e) { console.warn('Inject styles failed', e); }
            // Prefer new card-body to limit DOM replacement
            let newCardBody = qs('.col-md-9 .card.content-border .card-body', doc);
            let newCard = qs('.col-md-9 .card.content-border', doc);
            let newColumn = qs('.col-md-9', doc);
            // Inject any <style> tags that are inside the fragment (not just <head>)
            try {
              const fragmentRoot = newCardBody || newCard || newColumn || null;
              if (fragmentRoot) {
                const localStyles = fragmentRoot.querySelectorAll ? fragmentRoot.querySelectorAll('style') : [];
                localStyles.forEach(function(s){
                  const text = s.textContent || '';
                  const exists = Array.from(document.head.querySelectorAll('style')).some(function(hs){ return hs.textContent === text; });
                  if(!exists){
                    const clone = document.createElement('style');
                    clone.textContent = text;
                    clone.setAttribute('data-ajax-injected', '1');
                    document.head.appendChild(clone);
                  }
                });
              }
            } catch(e) { console.warn('Inject fragment styles failed', e); }
            // Force browser to apply injected styles before DOM update
            document.body.offsetHeight; // Force reflow
            // Small delay to ensure styles are fully applied
            requestAnimationFrame(function(){
              if(newCardBody && cardBody){
                cardBody.innerHTML = newCardBody.innerHTML;
              } else if(newCard && card){
                card.outerHTML = newCard.outerHTML;
              } else if(newColumn && column){
                column.innerHTML = newColumn.innerHTML;
              } else {
                container.innerHTML = html;
              }
              window.history.pushState({ partial: true }, '', url);
              // Re-bind ajax links after content swap
              initAjaxLinks();
              initAjaxLinks();
              // Execute any scripts included in the loaded fragment (they don't run via innerHTML)
              try {
                const fragmentRoot = newCardBody || newCard || newColumn || null;
                if(fragmentRoot){
                  const scripts = fragmentRoot.querySelectorAll('script');
                  scripts.forEach(function(s){
                    const ns = document.createElement('script');
                    if(s.src){ ns.src = s.src; }
                    if(s.type){ ns.type = s.type; }
                    ns.text = s.textContent || '';
                    document.body.appendChild(ns);
                    // remove after execution to keep DOM clean
                    setTimeout(function(){ document.body.removeChild(ns); }, 1000);
                  });
                }
              } catch (e) { console.warn('Execute scripts failed', e); }
              // Normalize tab state: ensure one tab is active to avoid layout issues
              try {
                const tabs = document.querySelector('#statusTabs');
                if(tabs){
                  const active = tabs.querySelector('.nav-link.active');
                  if(!active){
                    const first = tabs.querySelector('.nav-link');
                    if(first){
                      first.classList.add('active');
                      first.setAttribute('aria-selected', 'true');
                      const target = first.getAttribute('data-bs-target') || first.getAttribute('href');
                      if(target){
                        const pane = document.querySelector(target);
                        if(pane){ pane.classList.add('show','active'); }
                      }
                    }
                  }
                }
              } catch(e){ console.warn('Normalize tabs failed', e); }
            });
          })
          .catch(function(err){ console.error('AJAX load failed', err); window.location.href = url; })
          .finally(function(){
            // Smooth fade-in
            requestAnimationFrame(function(){
              container.style.opacity = '1';
              container.style.transition = 'opacity 160ms ease';
              // Release minHeight after transition
              setTimeout(function(){ container.style.minHeight = ''; }, 200);
            });
          });
      });
    });
  }
  window.addEventListener('popstate', function(){
    const url = window.location.href;
    const column = qs('.col-md-9');
    const cardBody = qs('.col-md-9 .card.content-border .card-body');
    const container = cardBody || column;
    if(!container) return;
    fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' }})
      .then(function(res){ return res.text(); })
      .then(function(html){
        const parser = new DOMParser();
        const doc = parser.parseFromString(html, 'text/html');
        // Inject head styles from the fetched document
        try {
          const styles = doc.querySelectorAll('head style');
          styles.forEach(function(s){
            const text = s.textContent || '';
            const exists = Array.from(document.head.querySelectorAll('style')).some(function(hs){ return hs.textContent === text; });
            if(!exists){
              const clone = document.createElement('style');
              clone.textContent = text;
              clone.setAttribute('data-ajax-injected', '1');
              document.head.appendChild(clone);
            }
          });
        } catch(e){ console.warn('Inject styles failed (popstate)', e); }

        let newCardBody = qs('.col-md-9 .card.content-border .card-body', doc);
        let newColumn = qs('.col-md-9', doc);
        // Inject any <style> tags that are inside the fragment so styles apply
        try {
          const fragmentRoot = newCardBody || newColumn || null;
          if (fragmentRoot) {
            const localStyles = fragmentRoot.querySelectorAll ? fragmentRoot.querySelectorAll('style') : [];
            localStyles.forEach(function(s){
              const text = s.textContent || '';
              const exists = Array.from(document.head.querySelectorAll('style')).some(function(hs){ return hs.textContent === text; });
              if(!exists){
                const clone = document.createElement('style');
                clone.textContent = text;
                clone.setAttribute('data-ajax-injected', '1');
                document.head.appendChild(clone);
              }
            });
          }
        } catch(e){ console.warn('Inject fragment styles failed (popstate)', e); }

        if(newCardBody && cardBody){
          cardBody.innerHTML = newCardBody.innerHTML;
        } else if(newColumn && column){
          column.innerHTML = newColumn.innerHTML;
        } else {
          container.innerHTML = html;
        }
        initAjaxLinks();
      })
      .catch(function(err){ console.error('AJAX popstate failed', err); });
  });
  document.addEventListener('DOMContentLoaded', function(){ initAjaxLinks(); });
})();
