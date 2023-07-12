<?php

namespace App\View\Components\Member;

use Illuminate\View\Component;
use App\Models\Card as CardModel;
use App\Models\Member as MemberModel;

class Card extends Component
{
    /**
     * @var CardModel $card - Card
     * @var MemberModel $member - Member
     * @var id $id - ID
     * @var bool $flippable - Card is flippable
     * @var bool $links - Card links to rewards
     * @var bool $showQr - Show QR
     * @var bool $showBalance - Show balance
     * @var string $customLink - Custom link for card
     * @var string $element_id - HTML element id
     * @var string $urlToEarnPoints - URL to earn points
     * @var string $type - Card type
     * @var string $icon - Icon
     * @var string $bgImage - Background image
     * @var string $bgColor - Background color
     * @var string $bgColorOpacity - Background color opacity
     * @var string $textColor - Text color
     * @var string $textLabelColor - Text label color
     * @var string $qrColorLight - QR light color (background)
     * @var string $qrColorDark - QR dark color (foreground)
     * @var int $balance - Card balance
     * @var string $logo - Logo
     * @var string $contentHead - Content head
     * @var string $contentTitle - Content title
     * @var string $contentDescription - Content description
     * @var string $identifier - Identifier
     * @var date $issueDate - Issue date
     * @var date $expirationDate - Expiration date
     * @var bool $authCheck - Is member authenticated
     * @var string $urlToEarnPoints - The url for the staff member to credit points
     */
    public 
        $card,
        $member,
        $id,
        $flippable,
        $links,
        $showQr,
        $showBalance,
        $customLink,
        $element_id,
        $type,
        $icon,
        $bgImage,
        $bgColor,
        $bgColorOpacity,
        $textColor,
        $textLabelColor,
        $qrColorLight,
        $qrColorDark,
        $balance,
        $logo,
        $contentHead,
        $contentTitle,
        $contentDescription,
        $identifier,
        $issueDate,
        $expirationDate,
        $authCheck,
        $urlToEarnPoints;

    /**
     * Create a new component instance.
     *
     * @param  CardModel  $card
     * @param  ?MemberModel  $member
     * @param  int  $id
     * @param  bool  $flippable
     * @param  bool  $links
     * @param  bool  $showQr
     * @param  bool  $showBalance
     * @param  string  $customLink
     * @param  string  $element_id
     * @param  string  $type
     * @param  string  $icon
     * @param  string  $bgImage
     * @param  string  $bgColor
     * @param  string  $bgColorOpacity
     * @param  string  $textColor
     * @param  string  $textLabelColor
     * @param  string  $qrColorLight
     * @param  string  $qrColorDark
     * @param  int  $balance
     * @param  string  $contentHead
     * @param  string  $contentTitle
     * @param  string  $contentDescription
     * @param  string  $image
     * @param  string  $imageClass
     * @param  string  $identifier
     * @param  date  $issueDate
     * @param  date  $expirationDate
     * @return void
     */
    public function __construct(
        $card = null,
        $member = null,
        $id = null,
        $flippable = false,
        $links = true,
        $showQr = true,
        $showBalance = true,
        $customLink = null,
        $element_id = null,
        $type = 'loyalty',
        $icon = null,
        $bgImage = null,
        $bgColor = null,
        $bgColorOpacity = null,
        $textColor = null,
        $textLabelColor = null,
        $qrColorLight = null,
        $qrColorDark = null,
        $balance = 0,
        $logo = null,
        $contentHead = null,
        $contentTitle = null,
        $contentDescription = null,
        $identifier = null,
        $issueDate = null,
        $expirationDate = null,
    ) {
        $this->card = $card;
        $this->member = $member ?? auth('member')->user();
        $this->id = $id ?? $card->id;
        $this->flippable = $flippable;
        $this->links = $links;
        $this->showQr = $showQr;
        $this->showBalance = $showBalance;
        $this->customLink = $customLink;
        $this->element_id = $element_id ?? 'card_'.unique_code(12);
        $this->type = $type ?? $card->type;
        $this->icon = $icon ?? $card->icon;
        $this->bgImage = $bgImage ?? $card->getImageUrl('background', 'sm');
        $this->bgColor = $bgColor ?? $card->bg_color;
        $this->bgColorOpacity = $bgColorOpacity ?? $card->bg_color_opacity;
        $this->textColor = $textColor ?? $card->text_color;
        $this->textLabelColor = $textLabelColor ?? $card->text_label_color;
        $this->qrColorLight = $qrColorLight ?? $card->qr_color_light;
        $this->qrColorDark = $qrColorDark ?? $card->qr_color_dark;
        $this->balance = $balance;
        $this->logo = $logo;
        $this->contentHead = $contentHead ?? $card->head;
        $this->contentTitle = $contentTitle ?? $card->title;
        $this->contentDescription = $contentDescription ?? $card->description;
        $this->identifier = $identifier ?? $card->unique_identifier;
        $this->issueDate = $issueDate ?? $card->issue_date;
        $this->expirationDate = $expirationDate ?? $card->expiration_date;
        $this->authCheck = isset($this->member);
        if ($this->showBalance) {
            $this->balance = ($this->member) ? $card->getMemberBalance($this->member) : $card->getMemberBalance(null);
        }
        $this->urlToEarnPoints = ($this->authCheck) ? route('staff.earn.points', ['member_identifier' => $this->member->unique_identifier, 'card_identifier' => $this->identifier]) : '';
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.member.card');
    }
}
