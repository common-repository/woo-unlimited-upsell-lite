!function(e){"use strict";var n={2:function(e){return e?"dva":"dve"},3:function(){return"tri"},4:function(){return"štyri"}};e.fn.select2.locales.sk={formatNoMatches:function(){return"Nenašli sa žiadne položky"},formatInputTooShort:function(e,t){var o=t-e.length;return 1==o?"Prosím, zadajte ešte jeden znak":o<=4?"Prosím, zadajte ešte ďalšie "+n[o](!0)+" znaky":"Prosím, zadajte ešte ďalších "+o+" znakov"},formatInputTooLong:function(e,t){var o=e.length-t;return 1==o?"Prosím, zadajte o jeden znak menej":o>=2&&o<=4?"Prosím, zadajte o "+n[o](!0)+" znaky menej":"Prosím, zadajte o "+o+" znakov menej"},formatSelectionTooBig:function(e){return 1==e?"Môžete zvoliť len jednu položku":e>=2&&e<=4?"Môžete zvoliť najviac "+n[e](!1)+" položky":"Môžete zvoliť najviac "+e+" položiek"},formatLoadMore:function(e){return"Načítavajú sa ďalšie výsledky…"},formatSearching:function(){return"Vyhľadávanie…"}},e.extend(e.fn.select2.defaults,e.fn.select2.locales.sk)}(jQuery);